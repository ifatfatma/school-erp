<?php

namespace App\Services;

use App\Models\AiBotFaq;
use App\Models\AiBotLog;
use App\Models\AiBotSetting;
use App\Models\School;
use App\Models\WebsiteSetting;
use Illuminate\Support\Str;

class AiBotService
{
    /**
     * Process user query and return intelligent response
     */
    public function answerQuery(string $query, ?int $schoolId = null, ?string $sessionId = null, ?int $userId = null, ?string $ip = null): array
    {
        $cleanQuery = trim($query);
        $school = $this->resolveSchool($schoolId);
        $settings = $this->resolveSettings($school?->id);

        if (empty($cleanQuery)) {
            return $this->buildWelcomePayload($school, $settings);
        }

        $normalized = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleanQuery));
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $tokens = array_filter(explode(' ', $normalized), fn($t) => strlen($t) > 2);

        // 1. Check small talk / greetings
        if ($this->isGreeting($normalized)) {
            $response = $this->buildGreetingResponse($school, $settings);
            $this->logInteraction($school?->id, $userId, $sessionId, $cleanQuery, $response['reply'], 'greeting', null, 1.0, $ip);
            return $response;
        }

        // 2. Check WhatsApp / Human escalation request
        if ($this->isHumanEscalationRequest($normalized)) {
            $response = $this->buildWhatsAppEscalationResponse($school, $settings, $cleanQuery);
            $this->logInteraction($school?->id, $userId, $sessionId, $cleanQuery, $response['reply'], 'whatsapp_escalation', null, 1.0, $ip, true);
            return $response;
        }

        // 3. Search Database FAQs (School-specific + Global)
        $dbFaqs = AiBotFaq::where('is_active', true)
            ->where(function ($q) use ($school) {
                if ($school) {
                    $q->where('school_id', $school->id)->orWhereNull('school_id');
                } else {
                    $q->whereNull('school_id');
                }
            })
            ->get();

        $bestFaq = null;
        $highestScore = 0;

        foreach ($dbFaqs as $faq) {
            $score = $this->computeMatchScore($normalized, $tokens, $faq->question, $faq->keywords, $faq->category);
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestFaq = $faq;
            }
        }

        // 4. Default Built-in Knowledge Base if no custom DB FAQ or score too low
        if (!$bestFaq || $highestScore < 35) {
            $builtinMatch = $this->searchBuiltinKnowledge($normalized, $tokens, $school, $settings);
            if ($builtinMatch && $builtinMatch['score'] > $highestScore) {
                $response = $this->formatBuiltinResponse($builtinMatch, $school, $settings, $cleanQuery);
                $this->logInteraction($school?->id, $userId, $sessionId, $cleanQuery, $response['reply'], $builtinMatch['category'], null, $builtinMatch['score'] / 100.0, $ip);
                return $response;
            }
        }

        if ($bestFaq && $highestScore >= 35) {
            $bestFaq->increment('clicks_count');
            $replyText = $this->substituteVariables($bestFaq->answer, $school, $settings);
            $whatsappUrl = $this->buildWhatsAppUrl($settings->whatsapp_number ?? '+919331805678', $cleanQuery, $school?->name);

            $response = [
                'reply' => $replyText,
                'category' => $bestFaq->category,
                'confidence' => min(100, $highestScore),
                'action' => $bestFaq->action_label ? [
                    'label' => $bestFaq->action_label,
                    'url' => $this->substituteVariables($bestFaq->action_url ?? '#', $school, $settings),
                ] : null,
                'whatsapp' => [
                    'number' => $settings->whatsapp_number ?? '+919331805678',
                    'url' => $whatsappUrl,
                    'label' => 'Chat on WhatsApp with Support',
                ],
                'suggestions' => $this->getCategorySuggestions($bestFaq->category, $school),
            ];

            $this->logInteraction($school?->id, $userId, $sessionId, $cleanQuery, $replyText, $bestFaq->category, $bestFaq->id, $highestScore / 100.0, $ip);
            return $response;
        }

        // 5. Fallback Intelligent Help Response (No direct match)
        $fallback = $this->buildFallbackResponse($school, $settings, $cleanQuery);
        $this->logInteraction($school?->id, $userId, $sessionId, $cleanQuery, $fallback['reply'], 'fallback', null, 0.2, $ip, true);
        return $fallback;
    }

    /**
     * Compute match score between user query and FAQ entry
     */
    protected function computeMatchScore(string $query, array $tokens, string $question, ?string $keywords, string $category): int
    {
        $score = 0;
        $qLower = strtolower($question);

        // Exact match
        if (trim($query) === trim($qLower)) {
            return 100;
        }

        // Question contains full query or vice-versa
        if (str_contains($qLower, $query) || str_contains($query, $qLower)) {
            $score += 60;
        }

        // Keyword matches
        $kwList = array_map('trim', explode(',', strtolower($keywords ?? '')));
        $matchedKwCount = 0;
        foreach ($kwList as $kw) {
            if (!empty($kw) && (str_contains($query, $kw) || in_array($kw, $tokens))) {
                $matchedKwCount++;
                $score += 25;
            }
        }

        // Token intersection
        $qTokens = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $qLower)));
        $commonTokens = array_intersect($tokens, $qTokens);
        $score += (count($commonTokens) * 15);

        // Category relevance
        if (str_contains($query, $category)) {
            $score += 15;
        }

        return min(98, $score);
    }

    /**
     * Built-in Comprehensive Knowledge Base
     */
    protected function searchBuiltinKnowledge(string $query, array $tokens, ?School $school, AiBotSetting $settings): ?array
    {
        $schoolName = $school?->name ?? 'Our Institution';
        $phone = $school?->phone ?? $settings->support_phone ?? '+91 93318 05678';
        $email = $school?->email ?? $settings->support_email ?? 'support@school.com';
        $address = $school?->address ?? 'Main Campus Office';
        $applyUrl = $school?->code ? "/s/{$school->code}/admissions/apply" : "/admissions/apply";
        $trackUrl = $school?->code ? "/s/{$school->code}/admissions/track" : "/admissions/track";
        $portalUrl = $school?->code ? "/s/{$school->code}/portal/login" : "/login";

        $kb = [
            // ADMISSION
            [
                'category' => 'admission',
                'keywords' => ['admission', 'apply', 'admit', 'application', 'enroll', 'form', 'eligibility', 'criteria', 'documents', 'intake', 'join'],
                'reply' => "### 🎓 Admissions at {$schoolName}\n\nWe welcome new student applications across Nursery, Primary, Secondary, and Senior Secondary classes.\n\n* **Online Application:** You can submit an application directly online in less than 5 minutes.\n* **Required Documents:** Birth certificate, previous report card, transfer certificate (TC), passport photos, and guardian ID proof.\n* **Application Tracking:** You can track the real-time status of your submitted application anytime using your Application Number.\n\nClick below to begin your online admission application or connect with our counselor directly.",
                'action' => ['label' => 'Apply for Admission Online', 'url' => $applyUrl],
                'suggestions' => ['Track my admission application', 'What are the admission criteria?', 'What are the school fees?'],
            ],
            [
                'category' => 'admission_track',
                'keywords' => ['track', 'status', 'check status', 'application status', 'admission status', 'track application'],
                'reply' => "### 🔍 Track Your Admission Application\n\nYou can verify the status of your online application (Submitted, Under Review, Approved, or Enrolled) using our live tracker.\n\nPlease keep your **Application Number** (e.g. `ADM-2026-XXXX`) ready.",
                'action' => ['label' => 'Open Application Tracker', 'url' => $trackUrl],
                'suggestions' => ['How do I apply for admission?', 'Contact admissions office on WhatsApp', 'School fee details'],
            ],

            // FEES & PAYMENTS
            [
                'category' => 'fees',
                'keywords' => ['fee', 'fees', 'tuition', 'payment', 'pay', 'dues', 'installments', 'receipt', 'upi', 'online payment', 'charges', 'cost'],
                'reply' => "### 💳 School Fees & Online Payments\n\n{$schoolName} provides secure digital fee payments with instant automated receipts:\n\n* **Payment Modes:** UPI (GPay, PhonePe, Paytm, BHIM), Net Banking, Credit/Debit Cards, and Cash at the School Accounts counter.\n* **Student/Parent Portal:** Log in to your portal account to view pending dues, semester fee breakdowns, and download official fee receipts.\n* **Support Desk:** For fee concession inquiries, installment plans, or receipt verification, contact the accounts office.",
                'action' => ['label' => 'Go to Student/Parent Portal', 'url' => '/login'],
                'suggestions' => ['How to view fee receipts?', 'Can I pay via UPI?', 'Connect with Accounts Office on WhatsApp'],
            ],

            // BIOMETRIC & ATTENDANCE
            [
                'category' => 'attendance',
                'keywords' => ['attendance', 'biometric', 'scanner', 'face recognition', 'fingerprint', 'check in', 'present', 'absent', 'late', 'punch'],
                'reply' => "### ⚡ Biometric & AI Vision Attendance\n\n{$schoolName} uses high-speed AI Face Recognition and Biometric terminals for seamless, hands-free attendance:\n\n* **Check-in Timing:** Gates open from 07:30 AM. Cut-off time for standard arrival is 08:30 AM (arrivals past cutoff are logged as *Late*).\n* **Instant Notifications:** As soon as a student or staff member checks in, automated notifications and SMS/portal updates are sent to parents.\n* **One Face = One Person:** Advanced anti-proxy and security verification prevents duplicate or proxy punches.",
                'action' => ['label' => 'View Live Scanner Console', 'url' => '/biometrics'],
                'suggestions' => ['What is the late arrival cutoff time?', 'How to apply for student leave?', 'Contact Attendance Office'],
            ],

            // LEAVE APPLICATION
            [
                'category' => 'leave',
                'keywords' => ['leave', 'sick leave', 'absent', 'leave application', 'vacation', 'holiday', 'apply leave'],
                'reply' => "### 📝 Student & Staff Leave Applications\n\n* **Parent/Student Portal:** Parents can submit a student leave request online stating the reason (Medical, Family, Personal) along with optional doctor's note.\n* **Teacher & Admin Review:** Class teachers and coordinators review and approve leave requests in real time, updating the ERP attendance record automatically.\n* **Emergency Absence:** For urgent same-day leaves, please inform the school office via WhatsApp or phone.",
                'action' => ['label' => 'Submit Leave via Portal', 'url' => '/login'],
                'suggestions' => ['School office timings', 'Check today\'s attendance', 'Chat on WhatsApp'],
            ],

            // EXAMS & RESULTS
            [
                'category' => 'exams',
                'keywords' => ['exam', 'exams', 'test', 'result', 'results', 'marks', 'grade', 'report card', 'schedule', 'datesheet', 'timetable', 'exam routine'],
                'reply' => "### 📊 Examinations & Student Results\n\n* **Exam Timetables:** Comprehensive date sheets, hall tickets, and seating arrangements are published in the Student Portal and Website Announcements prior to each term.\n* **Online Report Cards:** Semester results, subject grades, GPA, and teacher remarks can be viewed and downloaded in PDF format directly from the Parent/Student Portal.\n* **Result Inquiries:** For marks re-evaluation or queries, contact the Academic Coordinator.",
                'action' => ['label' => 'Check Exam Results Portal', 'url' => '/login'],
                'suggestions' => ['When are the next examinations?', 'How to download report card?', 'Contact Academic Office'],
            ],

            // TIMETABLE & CLASSES
            [
                'category' => 'timetable',
                'keywords' => ['timetable', 'routine', 'class routine', 'period', 'periods', 'subjects', 'schedule', 'class schedule', 'online class'],
                'reply' => "### 📅 Class Timetable & Routine\n\n* Daily class routines with assigned subject teachers, lab sessions, sports, and library periods are accessible via the Student and Teacher Portals.\n* In case of teacher substitutions or special events, the daily schedule is automatically updated in real-time on student dashboards.",
                'action' => ['label' => 'View Portal Timetable', 'url' => '/login'],
                'suggestions' => ['How to join online classes?', 'School working hours', 'Ask on WhatsApp'],
            ],

            // TRANSPORT
            [
                'category' => 'transport',
                'keywords' => ['bus', 'van', 'transport', 'route', 'stops', 'pickup', 'drop', 'driver', 'bus fee'],
                'reply' => "### 🚌 School Transport & Fleet Services\n\n{$schoolName} operates an extensive fleet of GPS-monitored school buses covering major neighborhood routes:\n\n* **Safety First:** Experienced licensed drivers, bus marshals, speed governors, and CCTV surveillance.\n* **Route Mapping:** Route stops, pickup/drop timings, and assigned driver details are available in the ERP Transport module.\n* **Transport Desk:** For new route allocations or route changes, please contact our Transport Manager.",
                'action' => null,
                'suggestions' => ['Transport fee structure', 'Bus routes & stops', 'Contact Transport Desk on WhatsApp'],
            ],

            // HOSTEL
            [
                'category' => 'hostel',
                'keywords' => ['hostel', 'boarding', 'room', 'bed', 'warden', 'mess', 'dining', 'residence'],
                'reply' => "### 🏢 Hostel & Boarding Facilities\n\nWe provide modern, secure residential boarding for students with dedicated study hours, nutritious mess meals, sports facilities, and 24/7 warden supervision.\n\n* **Accommodations:** Separate boys' and girls' wings with comfortable furnished rooms.\n* **Hostel Inquiries:** Room availability and boarding fees can be obtained from the Hostel Warden office.",
                'action' => null,
                'suggestions' => ['Hostel fee breakdown', 'Visiting hours for parents', 'Chat on WhatsApp'],
            ],

            // CONTACT & OFFICE HOURS
            [
                'category' => 'contact',
                'keywords' => ['contact', 'phone', 'telephone', 'mobile', 'call', 'email', 'address', 'location', 'where', 'timing', 'hours', 'office', 'principal', 'helpdesk', 'inquiry'],
                'reply' => "### 🏫 Contact {$schoolName}\n\n* **📍 Campus Address:** {$address}\n* **📞 Phone:** {$phone}\n* **✉️ Email:** {$email}\n* **⏰ Office Working Hours:** {$settings->office_hours}\n* **💬 Live WhatsApp Support:** Available 24/7 for parents and prospective students.\n\nYou can tap below to start a live WhatsApp chat directly with our helpdesk team.",
                'action' => ['label' => 'Chat on WhatsApp Now', 'url' => $this->buildWhatsAppUrl($settings->whatsapp_number ?? '+919331805678', 'General Inquiry', $schoolName)],
                'suggestions' => ['How do I apply for admission?', 'Fee payment options', 'Location directions'],
            ],

            // STUDENT & PARENT PORTAL
            [
                'category' => 'portal',
                'keywords' => ['portal', 'login', 'password', 'forgot password', 'student portal', 'parent portal', 'account', 'sign in', 'user id'],
                'reply' => "### 📱 Student & Parent ERP Portal\n\nThe online portal provides 24/7 access to your student's complete academic life:\n\n* **Features:** Live attendance records, online fee payments & receipts, homework & assignments, exam marks, and notice board bulletins.\n* **Login Credentials:** Use your registered Student Admission ID / Guardian Email and password.\n* **Forgot Password:** Click *Forgot Password* on the login screen or contact the School IT Administrator to reset your access.",
                'action' => ['label' => 'Open Portal Login', 'url' => '/login'],
                'suggestions' => ['How to view fee dues?', 'Check attendance status', 'Contact IT Support on WhatsApp'],
            ],
        ];

        $best = null;
        $highest = 0;

        foreach ($kb as $item) {
            $score = 0;
            foreach ($item['keywords'] as $kw) {
                if (str_contains($query, $kw)) {
                    $score += 30;
                }
                if (in_array($kw, $tokens)) {
                    $score += 20;
                }
            }
            if ($score > $highest) {
                $highest = $score;
                $best = $item;
                $best['score'] = min(95, $score);
            }
        }

        return $best;
    }

    /**
     * Format builtin knowledge base match response
     */
    protected function formatBuiltinResponse(array $match, ?School $school, AiBotSetting $settings, string $userQuery): array
    {
        $whatsappUrl = $this->buildWhatsAppUrl($settings->whatsapp_number ?? '+919331805678', $userQuery, $school?->name);

        return [
            'reply' => $match['reply'],
            'category' => $match['category'],
            'confidence' => $match['score'],
            'action' => $match['action'] ?? null,
            'whatsapp' => [
                'number' => $settings->whatsapp_number ?? '+919331805678',
                'url' => $whatsappUrl,
                'label' => 'Chat on WhatsApp with Support',
            ],
            'suggestions' => $match['suggestions'] ?? ['Admissions 2026', 'Fee Payment', 'Contact Office'],
        ];
    }

    /**
     * Greeting response
     */
    protected function buildGreetingResponse(?School $school, AiBotSetting $settings): array
    {
        $schoolName = $school?->name ?? 'School ERP';
        $botName = $settings->bot_name ?? 'School AI Assistant';
        $whatsappUrl = $this->buildWhatsAppUrl($settings->whatsapp_number ?? '+919331805678', 'Hello, I would like to inquire about ' . $schoolName, $schoolName);

        $reply = "### 👋 Hello! Welcome to {$schoolName}\n\nI am your **{$botName}**, available 24/7 to assist students, parents, and visitors.\n\nHere are some common things I can help you with right away:\n* **Online Admissions & Application Tracking**\n* **School Fees, Due Dates & Payment Modes**\n* **Biometric Attendance & Leave Rules**\n* **Examinations, Datesheets & Report Cards**\n* **Campus Office Hours & Direct WhatsApp Support**\n\nHow can I help you today? You can type any question or tap a suggestion below!";

        $applyUrl = $school?->code ? "/s/{$school->code}/admissions/apply" : "/admissions/apply";

        return [
            'reply' => $reply,
            'category' => 'greeting',
            'confidence' => 100,
            'action' => ['label' => 'Apply for Admission', 'url' => $applyUrl],
            'whatsapp' => [
                'number' => $settings->whatsapp_number ?? '+919331805678',
                'url' => $whatsappUrl,
                'label' => 'Chat on WhatsApp with Support',
            ],
            'suggestions' => [
                'How do I apply for admission?',
                'How can I pay school fees?',
                'What are the campus timings?',
                'Connect with counselor on WhatsApp',
            ],
        ];
    }

    /**
     * WhatsApp escalation response
     */
    protected function buildWhatsAppEscalationResponse(?School $school, AiBotSetting $settings, string $userQuery): array
    {
        $schoolName = $school?->name ?? 'Our School';
        $num = $settings->whatsapp_number ?? '+919331805678';
        $whatsappUrl = $this->buildWhatsAppUrl($num, $userQuery, $schoolName);

        $reply = "### 💬 Connect with Human Support on WhatsApp\n\nOur human admissions counselors and administrative staff are ready to chat with you directly on **WhatsApp**!\n\n* **Support Desk Phone:** {$num}\n* **Hours:** {$settings->office_hours}\n* **Fast Response:** Tap the button below to open a direct WhatsApp chat window with your question already prepared.";

        return [
            'reply' => $reply,
            'category' => 'whatsapp_escalation',
            'confidence' => 100,
            'action' => [
                'label' => 'Open WhatsApp Chat Now',
                'url' => $whatsappUrl,
                'is_external' => true,
            ],
            'whatsapp' => [
                'number' => $num,
                'url' => $whatsappUrl,
                'label' => 'Open WhatsApp Chat Now',
            ],
            'suggestions' => [
                'What is the admission procedure?',
                'Check fee structure',
                'School office address',
            ],
        ];
    }

    /**
     * Fallback response when no confidence threshold reached
     */
    protected function buildFallbackResponse(?School $school, AiBotSetting $settings, string $userQuery): array
    {
        $schoolName = $school?->name ?? 'Our School';
        $num = $settings->whatsapp_number ?? '+919331805678';
        $whatsappUrl = $this->buildWhatsAppUrl($num, $userQuery, $schoolName);

        $reply = "### 🤝 I'm here to help!\n\nI couldn't find an exact match for *\"{$userQuery}\"*, but our staff is here to help you directly:\n\n* **1-Click WhatsApp:** Chat directly with our school admissions and support desk on WhatsApp.\n* **Helpdesk Phone:** {$num}\n* **Working Hours:** {$settings->office_hours}\n\nAlternatively, please select one of the popular topics below:";

        return [
            'reply' => $reply,
            'category' => 'fallback',
            'confidence' => 20,
            'action' => [
                'label' => 'Chat with Staff on WhatsApp',
                'url' => $whatsappUrl,
                'is_external' => true,
            ],
            'whatsapp' => [
                'number' => $num,
                'url' => $whatsappUrl,
                'label' => 'Chat with Staff on WhatsApp',
            ],
            'suggestions' => [
                'Admission procedure & eligibility',
                'Fee payment options',
                'Track admission application',
                'Campus timings and address',
            ],
        ];
    }

    /**
     * Initial welcome payload for the bot widget
     */
    public function getInitialConfig(?int $schoolId = null): array
    {
        $school = $this->resolveSchool($schoolId);
        $settings = $this->resolveSettings($school?->id);
        $schoolName = $school?->name ?? 'School ERP';

        $num = $settings->whatsapp_number ?? '+919331805678';
        $whatsappUrl = $this->buildWhatsAppUrl($num, 'Hello ' . $schoolName . ' Support, I would like to inquire about your school.', $schoolName);

        $categories = [
            [
                'id' => 'admission',
                'title' => 'Admissions & Enrollment',
                'icon' => 'mortarboard',
                'prompt' => 'How do I apply for admission?',
            ],
            [
                'id' => 'fees',
                'title' => 'Fees & Online Payments',
                'icon' => 'credit-card',
                'prompt' => 'What are the school fees and payment modes?',
            ],
            [
                'id' => 'attendance',
                'title' => 'Attendance & Biometrics',
                'icon' => 'person-check',
                'prompt' => 'What are the school timing and attendance rules?',
            ],
            [
                'id' => 'exams',
                'title' => 'Exams & Results',
                'icon' => 'clipboard-data',
                'prompt' => 'How can I check exam results and report cards?',
            ],
            [
                'id' => 'contact',
                'title' => 'Contact & Office Hours',
                'icon' => 'telephone',
                'prompt' => 'What are the school office hours and address?',
            ],
        ];

        return [
            'success' => true,
            'school' => [
                'id' => $school?->id,
                'name' => $schoolName,
                'code' => $school?->code,
                'logo' => $school?->logo_url,
                'phone' => $school?->phone,
                'email' => $school?->email,
            ],
            'bot' => [
                'name' => $settings->bot_name ?? 'School AI Assistant',
                'welcome_message' => $settings->welcome_message ?? "Hello! I am your 24/7 School AI Support Assistant. Ask me anything about admissions, fees, exams, attendance, or tap WhatsApp to speak with a counselor.",
                'is_enabled' => $settings->is_enabled,
                'enable_whatsapp' => $settings->enable_whatsapp_button,
                'enable_voice' => $settings->enable_voice_speech,
                'office_hours' => $settings->office_hours,
            ],
            'whatsapp' => [
                'number' => $num,
                'url' => $whatsappUrl,
            ],
            'quick_categories' => $categories,
            'default_suggestions' => [
                'How do I apply for admission?',
                'What are the school fee payment options?',
                'Track my admission status',
                'Chat on WhatsApp with Counselor',
            ],
        ];
    }

    /**
     * Resolve School Model
     */
    protected function resolveSchool(?int $schoolId): ?School
    {
        if ($schoolId) {
            return School::withoutGlobalScopes()->find($schoolId);
        }
        return School::withoutGlobalScopes()->first();
    }

    /**
     * Resolve AI Bot Settings
     */
    protected function resolveSettings(?int $schoolId): AiBotSetting
    {
        $setting = null;
        if ($schoolId) {
            $setting = AiBotSetting::where('school_id', $schoolId)->first();
        }
        if (!$setting) {
            $setting = AiBotSetting::whereNull('school_id')->first();
        }
        if (!$setting) {
            $setting = new AiBotSetting([
                'bot_name' => 'School AI Assistant',
                'welcome_message' => 'Hello! I am your 24/7 School AI Support Assistant. How can I help you today?',
                'whatsapp_number' => '+919331805678',
                'support_email' => 'support@school.com',
                'support_phone' => '+91 93318 05678',
                'office_hours' => 'Mon - Sat: 8:00 AM - 4:00 PM',
                'is_enabled' => true,
                'enable_whatsapp_button' => true,
                'enable_voice_speech' => true,
            ]);
        }
        return $setting;
    }

    /**
     * Format WhatsApp URL
     */
    public function buildWhatsAppUrl(string $phone, string $text, ?string $schoolName = null): string
    {
        $cleanPhone = preg_replace('/[^\d]/', '', $phone);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        $intro = $schoolName ? "[{$schoolName} Support] " : "";
        $message = $intro . "Inquiry: " . $text;

        return 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($message);
    }

    /**
     * Substitute template placeholders
     */
    protected function substituteVariables(string $text, ?School $school, AiBotSetting $settings): string
    {
        $replace = [
            '{school_name}' => $school?->name ?? 'Our School',
            '{school_code}' => $school?->code ?? '',
            '{phone}' => $school?->phone ?? $settings->support_phone ?? '+91 93318 05678',
            '{email}' => $school?->email ?? $settings->support_email ?? 'admissions@school.com',
            '{address}' => $school?->address ?? 'Main Campus',
            '{whatsapp}' => $settings->whatsapp_number ?? '+919331805678',
            '{hours}' => $settings->office_hours ?? 'Mon-Sat 8AM-4PM',
        ];

        return str_replace(array_keys($replace), array_values($replace), $text);
    }

    /**
     * Check if query is simple greeting
     */
    protected function isGreeting(string $query): bool
    {
        $greetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening', 'namaste', 'assalamu alaikum', 'hola', 'start', 'help'];
        $q = trim($query);
        foreach ($greetings as $g) {
            if ($q === $g || str_starts_with($q, $g . ' ') || str_ends_with($q, ' ' . $g)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if query asks for human / WhatsApp
     */
    protected function isHumanEscalationRequest(string $query): bool
    {
        $escalations = ['whatsapp', 'human', 'agent', 'real person', 'counselor', 'call', 'talk to someone', 'speak to staff', 'customer care', 'support number'];
        foreach ($escalations as $e) {
            if (str_contains($query, $e)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get suggestions based on matched category
     */
    protected function getCategorySuggestions(string $category, ?School $school): array
    {
        return match ($category) {
            'admission' => ['How do I track my application?', 'What are the required documents?', 'Connect on WhatsApp'],
            'fees' => ['How can I pay via UPI?', 'How to download fee receipt?', 'Connect with Accounts Office'],
            'attendance' => ['What is late arrival time?', 'How to apply for sick leave?', 'Check attendance status'],
            'exams' => ['How to view online report cards?', 'When is next term exam?', 'Ask academic coordinator'],
            default => ['Admissions criteria', 'School fee payment', 'Office address & hours', 'Chat on WhatsApp'],
        };
    }

    /**
     * Log interaction for analytics
     */
    protected function logInteraction(?int $schoolId, ?int $userId, ?string $sessionId, string $query, string $reply, ?string $category, ?int $faqId, float $confidence, ?string $ip, bool $whatsapp = false): void
    {
        try {
            AiBotLog::create([
                'school_id' => $schoolId,
                'user_id' => $userId,
                'session_id' => $sessionId ?? Str::random(16),
                'user_query' => $query,
                'bot_response' => Str::limit($reply, 1000),
                'matched_category' => $category,
                'matched_faq_id' => $faqId,
                'confidence_score' => $confidence,
                'escalated_to_whatsapp' => $whatsapp,
                'user_ip' => $ip,
            ]);
        } catch (\Throwable $e) {
            // Silently ignore log insertion failures
        }
    }
}
