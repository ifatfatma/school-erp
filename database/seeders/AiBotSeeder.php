<?php

namespace Database\Seeders;

use App\Models\AiBotFaq;
use App\Models\AiBotSetting;
use App\Models\School;
use Illuminate\Database\Seeder;

class AiBotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create global default AI bot settings
        AiBotSetting::updateOrCreate(
            ['school_id' => null],
            [
                'bot_name' => 'Campus AI Assistant',
                'welcome_message' => 'Hello! I am your 24/7 School AI Support Assistant. Ask me anything about admissions, fees, timetable, attendance, exams, or connect on WhatsApp.',
                'whatsapp_number' => '+919331805678',
                'whatsapp_default_message' => 'Hello School Support Desk, I would like to make an inquiry.',
                'support_email' => 'support@school.com',
                'support_phone' => '+91 93318 05678',
                'office_hours' => 'Mon - Sat: 8:00 AM - 4:00 PM',
                'is_enabled' => true,
                'enable_whatsapp_button' => true,
                'enable_voice_speech' => true,
            ]
        );

        $schools = School::withoutGlobalScopes()->get();
        foreach ($schools as $school) {
            AiBotSetting::updateOrCreate(
                ['school_id' => $school->id],
                [
                    'bot_name' => $school->name . ' AI Assistant',
                    'welcome_message' => "Welcome to {$school->name}! I am your 24/7 AI Support Assistant. How can I help you today?",
                    'whatsapp_number' => $school->phone ?? '+919331805678',
                    'whatsapp_default_message' => "Hello {$school->name} Support, I have an inquiry.",
                    'support_email' => $school->email ?? 'admissions@school.com',
                    'support_phone' => $school->phone ?? '+91 93318 05678',
                    'office_hours' => 'Mon - Sat: 8:00 AM - 4:00 PM',
                    'is_enabled' => true,
                    'enable_whatsapp_button' => true,
                    'enable_voice_speech' => true,
                ]
            );
        }

        // 2. Global Knowledge Base FAQs
        $faqs = [
            // ADMISSION
            [
                'category' => 'admission',
                'question' => 'How can I apply for admission?',
                'answer' => "You can apply for admission directly through our online admissions portal in 3 simple steps:\n1. Fill in the student and guardian information.\n2. Upload the required documents (birth certificate, previous report card, transfer certificate, passport photo).\n3. Submit the application and receive an instant tracking ID.\n\nApplications are reviewed within 48 business hours by our admissions committee.",
                'keywords' => 'apply, admission, online application, register, enroll, admission procedure, how to apply',
                'action_label' => 'Apply Online Now',
                'action_url' => '/apply-admission',
                'sort_order' => 1,
            ],
            [
                'category' => 'admission',
                'question' => 'What documents are required for school admission?',
                'answer' => "The following documents are required for new admissions:\n* Student's Birth Certificate (Municipal / Government issued)\n* Transfer Certificate (TC) from the previous school (Class 1 onwards)\n* Previous Academic Year's Progress Report / Marksheet\n* 4 Recent Passport-size Photographs of the student\n* Aadhar Card / National ID of Student & Guardians\n* Caste / Category Certificate (if applicable)\n* Blood Group & Medical Fitness Certificate",
                'keywords' => 'documents, required documents, birth certificate, transfer certificate, tc, papers, criteria',
                'action_label' => 'Submit Documents Online',
                'action_url' => '/apply-admission',
                'sort_order' => 2,
            ],
            [
                'category' => 'admission',
                'question' => 'How do I track my submitted admission application?',
                'answer' => "You can track the progress of your application at any time:\n1. Navigate to the **Track Application** page.\n2. Enter your unique **Application Number** (received via SMS/Email after submission).\n3. View your real-time status: Submitted, Under Review, Documents Verified, Interview Scheduled, or Admission Confirmed.",
                'keywords' => 'track, status, check status, application number, admission tracking, result',
                'action_label' => 'Track Application Status',
                'action_url' => '/admissions/track',
                'sort_order' => 3,
            ],

            // FEES
            [
                'category' => 'fees',
                'question' => 'What are the available school fee payment options?',
                'answer' => "We provide multiple secure, digital payment channels:\n* **UPI & QR Code:** Instant payment via Google Pay, PhonePe, Paytm, or BHIM UPI.\n* **Net Banking & Credit/Debit Cards:** Integrated online payment gateway on the Student/Parent Portal.\n* **School Cash Counter:** Visit our accounts office during working hours (08:30 AM - 03:00 PM).\n* **Official Receipts:** Automated GST-compliant receipts are generated instantly and saved to your portal account.",
                'keywords' => 'fee, fees, pay, payment options, upi, qr code, google pay, phonepe, net banking, card',
                'action_label' => 'Open Fee Payment Portal',
                'action_url' => '/login',
                'sort_order' => 4,
            ],
            [
                'category' => 'fees',
                'question' => 'How do I download my fee receipt?',
                'answer' => "To download your fee payment receipt:\n1. Log in to the **Student / Parent Portal**.\n2. Click on the **Fees & Payments** menu.\n3. Locate the paid invoice under Payment History.\n4. Click the **Download Receipt** button to save a signed PDF copy.",
                'keywords' => 'fee receipt, invoice, download receipt, payment proof, receipt copy',
                'action_label' => 'View Fee Receipts',
                'action_url' => '/login',
                'sort_order' => 5,
            ],

            // BIOMETRIC & ATTENDANCE
            [
                'category' => 'attendance',
                'question' => 'What is the daily school arrival and late cutoff time?',
                'answer' => "Standard attendance timing:\n* **Campus Gates Open:** 07:30 AM\n* **Morning Assembly:** 08:00 AM\n* **Late Arrival Cutoff:** 08:30 AM (any biometric or AI face check-in recorded after 08:30 AM is automatically marked as **Late** in the ERP).\n* **Dispersal:** 02:30 PM (Primary) / 03:30 PM (Secondary & Higher)",
                'keywords' => 'timing, late, arrival time, morning assembly, cutoff, school hours, gate timing',
                'action_label' => null,
                'action_url' => null,
                'sort_order' => 6,
            ],
            [
                'category' => 'attendance',
                'question' => 'How does the AI Face Recognition Biometric Attendance work?',
                'answer' => "Our campus utilizes contactless AI Vision terminals at campus entry points:\n* Students and staff pause briefly in front of the scanner reticle.\n* The system performs secure multi-zone facial verification in under 1 second.\n* Attendance status (Present / Late) is recorded in real time and parents receive instant check-in confirmation.",
                'keywords' => 'biometric, face recognition, ai scanner, attendance machine, punch, check in',
                'action_label' => 'Open Biometric Terminal',
                'action_url' => '/biometrics',
                'sort_order' => 7,
            ],

            // EXAMS & RESULTS
            [
                'category' => 'exams',
                'question' => 'How can parents view exam results and report cards?',
                'answer' => "Exam report cards are published online at the conclusion of each academic term:\n1. Log in to the **Student or Parent Portal**.\n2. Select **Examinations & Results** from the sidebar.\n3. Choose the academic term (Quarterly, Half-Yearly, Annual).\n4. View subject-wise marks, percentage, overall grade, teacher feedback, and download the official signed Report Card PDF.",
                'keywords' => 'results, marks, report card, exam result, grades, percentage, marksheet',
                'action_label' => 'Portal Login',
                'action_url' => '/login',
                'sort_order' => 8,
            ],

            // CONTACT & SUPPORT
            [
                'category' => 'contact',
                'question' => 'How can I connect with the school administration or principal?',
                'answer' => "You can reach our school administration through several channels:\n* **WhatsApp Support:** Instant messaging with our Helpdesk at {whatsapp}.\n* **Telephone:** Call our front desk at {phone}.\n* **Email:** Write to us at {email}.\n* **In-Person Visit:** Office hours are {hours} at our {address}.\n* **Parent-Teacher Meetings (PTM):** Scheduled bi-monthly with class educators.",
                'keywords' => 'contact, phone, call, office, address, principal, meet, helpdesk, number, appointment',
                'action_label' => 'Chat on WhatsApp',
                'action_url' => 'https://wa.me/919331805678',
                'sort_order' => 9,
            ],
        ];

        foreach ($faqs as $f) {
            AiBotFaq::updateOrCreate(
                ['question' => $f['question']],
                $f
            );
        }
    }
}
