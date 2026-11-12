<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\WebsiteInquiry;
use App\Models\WebsiteSetting;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaasPlatformController extends Controller
{
    /**
     * Get SaaS Subscription Plans & Pricing
     */
    public function getPlans()
    {
        $setting = PlatformSetting::where('key', 'plans')->first();
        if ($setting && !empty($setting->content)) {
            return response()->json([
                'success' => true,
                'data' => $setting->content,
            ]);
        }

        $plans = self::getDefaultPlans();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * Default SaaS Subscription Plans
     */
    public static function getDefaultPlans(): array
    {
        return [
            [
                'id' => 'starter',
                'name' => 'Starter / Basic',
                'badge' => 'Small Institutions',
                'price_monthly' => 2999,
                'price_annual' => 29990,
                'student_limit' => '300',
                'teacher_limit' => '25',
                'features' => [
                    'Student Information Management',
                    'Daily Attendance & SMS Alerts',
                    'Fee Collection & Digital Receipts',
                    'Examination & Report Cards',
                    'Student & Parent Web Portal',
                    'Live Online Classes & Meeting Links',
                    'Standard Email Support',
                ],
                'popular' => false,
            ],
            [
                'id' => 'growth',
                'name' => 'Professional / Growth',
                'badge' => 'Most Popular',
                'price_monthly' => 5999,
                'price_annual' => 59990,
                'student_limit' => '1200',
                'teacher_limit' => '80',
                'features' => [
                    'Everything in Starter Plan',
                    'Live Online Classes (Google Meet / Zoom / MS Teams)',
                    'Instant Free In-Browser Video Rooms (Jitsi)',
                    'Class Lecture Recording Attachments',
                    'Online Admissions & Public Website',
                    'Digital Library Management',
                    'Hostel & Dormitory Allocation',
                    'Transport Routes & Vehicle Fleet Tracking',
                    'Online Fee Payment Gateway',
                    'Priority Email & Phone Support',
                ],
                'popular' => true,
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise / Multi-Branch',
                'badge' => 'Large Campuses',
                'price_monthly' => 11999,
                'price_annual' => 119990,
                'student_limit' => 'Unlimited',
                'teacher_limit' => 'Unlimited',
                'features' => [
                    'Everything in Professional Plan',
                    'Unlimited HD Live Virtual Classrooms & Recordings',
                    'Multi-Branch & Campus Aggregation',
                    'Dedicated Custom Domain / Subdomain',
                    'Custom ERP Modules & Branding',
                    'Automated Daily Cloud Backups',
                    'Dedicated Account Manager & 24/7 SLA',
                ],
                'popular' => false,
            ],
        ];
    }

    /**
     * Public School Registration / Free Trial Onboarding
     */
    public function registerSchool(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:150',
            'school_code' => 'required|string|max:20|alpha_dash|unique:schools,code',
            'admin_name' => 'required|string|max:100',
            'admin_email' => 'required|email|max:100|unique:users,email',
            'admin_phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'plan' => 'required|string|in:starter,growth,enterprise',
            'estimated_students' => 'nullable|numeric',
        ]);

        return DB::transaction(function () use ($validated) {
            // 1. Create School record
            $code = strtoupper(trim($validated['school_code']));
            $school = School::create([
                'name' => trim($validated['school_name']),
                'code' => $code,
                'email' => strtolower(trim($validated['admin_email'])),
                'phone' => trim($validated['admin_phone']),
                'city' => trim($validated['city']),
                'state' => $validated['state'] ? trim($validated['state']) : 'West Bengal',
                'country' => 'India',
                'address' => trim($validated['city']) . ' Campus',
                'status' => true,
                'subscription_plan' => strtolower(trim($validated['plan'])),
                'subscription_status' => 'active',
            ]);

            // 2. Generate a default secure password for the new School Admin
            $initialPassword = 'password123';

            // 3. Create School Administrator user account
            $user = User::create([
                'school_id' => $school->id,
                'name' => trim($validated['admin_name']),
                'username' => strtolower($code) . '_admin',
                'email' => strtolower(trim($validated['admin_email'])),
                'phone' => trim($validated['admin_phone']),
                'password' => Hash::make($initialPassword),
                'user_type' => 'school_admin',
                'status' => 1,
            ]);

            // Assign School Admin role if available
            $schoolAdminRole = \Spatie\Permission\Models\Role::where('name', 'School Admin')
                ->where('guard_name', 'sanctum')
                ->first();
            if ($schoolAdminRole) {
                $user->assignRole($schoolAdminRole);
            }

            // 4. Seed initial default website content for the new school
            $defaults = WebsiteManagerController::getDefaultSettings($school);
            foreach ($defaults as $sectionKey => $content) {
                WebsiteSetting::withoutGlobalScopes()->create([
                    'school_id' => $school->id,
                    'section_key' => $sectionKey,
                    'content' => $content,
                    'is_active' => true,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Congratulations! Your school has been registered successfully.',
                'data' => [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                    'school_code' => $school->code,
                    'portal_url' => "/s/{$school->code}",
                    'admin_login_url' => "/s/{$school->code}/login",
                    'admin_email' => $user->email,
                    'default_password' => $initialPassword,
                ],
            ], 201);
        });
    }

    /**
     * Submit SaaS Demo or Consultation Request
     */
    public function submitDemoRequest(Request $request)
    {
        $validated = $request->validate([
            'institution_name' => 'required|string|max:150',
            'contact_person' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'student_count' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:1000',
        ]);

        // Save into website inquiries with school_id 1 or platform context
        $rootSchool = School::first();
        WebsiteInquiry::create([
            'school_id' => $rootSchool ? $rootSchool->id : 1,
            'name' => $validated['contact_person'] . " ({$validated['institution_name']})",
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'subject' => "Demo Request for {$validated['institution_name']} (Students: " . ($validated['student_count'] ?? 'N/A') . ")",
            'message' => $validated['message'] ?? 'Interested in School ERP Cloud Platform Demo.',
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your interest! Our School ERP specialist will contact you within 24 hours to schedule your personalized live demo.',
        ]);
    }

    /**
     * Public Active Schools Directory (for finding school portals)
     */
    public function getDirectory()
    {
        $schools = School::where('status', true)
            ->get(['id', 'name', 'code', 'city', 'state', 'phone', 'email', 'logo'])
            ->map(function ($s) {
                $logoUrl = null;
                if ($s->logo) {
                    $raw = is_array($s->logo) ? reset($s->logo) : $s->logo;
                    if ($raw && is_string($raw) && $raw !== '[]' && $raw !== 'null') {
                        $logoUrl = str_starts_with($raw, 'http') ? $raw : asset('storage/' . ltrim($raw, '/'));
                    }
                }
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'code' => $s->code,
                    'city' => $s->city,
                    'state' => $s->state,
                    'logo' => $logoUrl,
                    'portal_url' => "/s/{$s->code}",
                    'student_login_url' => "/s/{$s->code}/portal/login",
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $schools,
        ]);
    }

    /**
     * Default Platform / Root Landing Page Content
     */
    public static function getDefaultPlatformSettings(): array
    {
        return [
            'general' => [
                'brand_name' => 'EduCloud ERP',
                'brand_tagline' => 'Next-Gen School Operating System',
                'support_email' => 'contact@schoolerpcloud.com',
                'security_badge' => 'ISO 27001 Certified • Bank-Grade Cloud Security',
                'footer_about' => 'The premier all-in-one cloud platform engineered for forward-thinking schools, academies, and multi-campus institutions. Manage admissions, examinations, finance, fleets, and portals in one secure ecosystem.',
                'social_linkedin' => 'https://linkedin.com',
                'social_twitter' => 'https://x.com',
                'social_github' => 'https://github.com',
            ],
            'hero' => [
                'badge' => 'Cloud Multi-Tenant School ERP 2.0',
                'heading_line1' => 'One Unified Cloud ERP for',
                'heading_line2' => 'Modern Educational Institutions.',
                'subheading' => 'Eliminate paper bottlenecks, automate admissions and fee collections, deliver intelligent report cards, and connect students, teachers, and parents through dedicated self-service portals.',
                'cta_register_text' => 'Register Your School Free',
                'cta_pricing_text' => 'View Pricing & Plans',
                'cta_directory_text' => 'Access School Portals',
            ],
            'stats' => [
                'stat1_value' => '100%',
                'stat1_label' => 'Multi-Tenant',
                'stat2_value' => '15+',
                'stat2_label' => 'Core Modules',
                'stat3_value' => '99.98%',
                'stat3_label' => 'Uptime SLA',
                'stat4_value' => '0 Days',
                'stat4_label' => 'Instant Setup',
            ],
            'architecture' => [
                'card_title' => 'EduCloud Architecture',
                'card_badge' => 'SaaS Ready',
                'card_subtitle' => 'Independent Tenant Portals & Data Isolation',
                'item1_title' => 'Root Platform (/)',
                'item1_desc' => 'Marketing, plans, school onboarding & platform billing',
                'item2_title' => 'School Tenant Portals (/s/:code)',
                'item2_desc' => 'Dedicated website, admissions, notice board & campus info',
                'item3_title' => 'Student & Parent Portal',
                'item3_desc' => 'Attendance, dues, fee receipts, report cards & timetables',
                'cta_button_text' => 'Launch Your Institution in 2 Minutes →',
            ],
            'plans' => self::getDefaultPlans(),
        ];
    }

    /**
     * Get Public Content for Root Landing Page
     */
    public function getPublicContent()
    {
        $defaults = self::getDefaultPlatformSettings();
        $saved = PlatformSetting::all()->keyBy('key');

        $content = [];
        foreach ($defaults as $key => $defaultVal) {
            if ($saved->has($key)) {
                $content[$key] = array_merge($defaultVal, (array) $saved->get($key)->content);
            } else {
                $content[$key] = $defaultVal;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $content,
        ]);
    }

    /**
     * Get Content for Admin Root Website CMS
     */
    public function getAdminContent()
    {
        return $this->getPublicContent();
    }

    /**
     * Update a Root Website CMS Section (Super Admin only)
     */
    public function updateSection(Request $request, string $section)
    {
        $defaults = self::getDefaultPlatformSettings();
        if (!array_key_exists($section, $defaults)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid platform section: {$section}",
            ], 422);
        }

        $validated = $request->validate([
            'content' => 'required|array',
        ]);

        $setting = PlatformSetting::updateOrCreate(
            ['key' => $section],
            ['content' => $validated['content']]
        );

        return response()->json([
            'success' => true,
            'message' => ucfirst($section) . ' section updated successfully!',
            'data' => $setting->content,
        ]);
    }

    /**
     * Reset a Root Website CMS Section to Defaults
     */
    public function resetSection(string $section)
    {
        $defaults = self::getDefaultPlatformSettings();
        if (!array_key_exists($section, $defaults)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid section.',
            ], 422);
        }

        PlatformSetting::where('key', $section)->delete();

        return response()->json([
            'success' => true,
            'message' => ucfirst($section) . ' section reset to defaults.',
            'data' => $defaults[$section],
        ]);
    }
}
