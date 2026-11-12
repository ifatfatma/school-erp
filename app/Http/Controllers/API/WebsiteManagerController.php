<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\School;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteManagerController extends Controller
{
    /**
     * Default fallback configuration for each website section
     */
    public static function getDefaultSettings(?School $school = null)
    {
        $name = $school ? $school->name : 'St. George English Day School';
        $code = $school ? $school->code : 'S001';
        $city = ($school && $school->city) ? $school->city : 'Kolkata';
        $state = ($school && $school->state) ? $school->state : 'West Bengal';
        $country = ($school && $school->country) ? $school->country : 'India';
        $address = ($school && $school->address) ? $school->address : 'Main Campus';
        $phone = ($school && $school->phone) ? $school->phone : '+91 033 5562 9768';
        $pincode = ($school && $school->postal_code) ? $school->postal_code : '700024';

        $slug = Str::slug($name);
        $email = ($school && $school->email) ? $school->email : "info@{$slug}.edu";
        $emailAdmissions = "admissions@{$slug}.edu";

        $logoUrl = null;
        if ($school && $school->logo) {
            $raw = is_array($school->logo) ? reset($school->logo) : $school->logo;
            if ($raw && is_string($raw) && $raw !== '[]' && $raw !== 'null' && $raw !== '{}') {
                $logoUrl = str_starts_with($raw, 'http') ? $raw : asset('storage/' . ltrim($raw, '/'));
            }
        }

        return [
            'general' => [
                'school_name' => $name,
                'school_code' => $code,
                'tagline' => 'Excellence • Integrity • Innovation',
                'description' => "Empowering students through comprehensive education, fostering intellectual growth, moral character, and lifelong leadership at {$name}.",
                'phone_primary' => $phone,
                'phone_secondary' => '+91 90079 63069',
                'email_primary' => $email,
                'email_admissions' => $emailAdmissions,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'pincode' => $pincode,
                'office_hours' => 'Mon - Sat: 8:00 AM - 3:30 PM',
                'logo' => $logoUrl,
                'social_facebook' => 'https://facebook.com',
                'social_instagram' => 'https://instagram.com',
                'social_youtube' => 'https://youtube.com',
                'social_twitter' => 'https://twitter.com',
            ],
            'hero' => [
                'badge_text' => 'Admissions Open for Academic Session 2026-27',
                'heading_line1' => 'Empowering Minds,',
                'heading_line2' => 'Shaping Futures.',
                'subheading' => "Welcome to {$name}. We deliver a blend of academic rigor, modern technological integration, and ethical leadership to prepare your child for tomorrow's world.",
                'cta_primary_text' => 'Apply for Admission',
                'cta_primary_link' => "/s/{$code}/admissions/apply",
                'cta_secondary_text' => 'Student / Parent Portal',
                'cta_secondary_link' => '/portal/login',
            ],
            'about' => [
                'badge' => 'About Our Institution',
                'heading' => "Welcome to {$name}",
                'subheading' => 'Nurturing potential and cultivating leadership for over two decades.',
                'paragraph1' => "Founded with a visionary commitment to provide accessible, high-caliber English-medium education, {$name} has established itself as an institution of repute in {$city}.",
                'paragraph2' => 'We combine high academic standards with moral values, co-curricular sports, and modern digital classrooms to prepare our students for global excellence.',
                'vision' => 'To be a premier learning center that inspires intellectual rigor, ethical values, emotional strength, and social empathy in every student to thrive in a globalized society.',
                'mission' => 'To empower each student with critical thinking capabilities, technological proficiency, physical fitness, and moral integrity through compassionate, research-backed pedagogy.',
            ],
            'principal' => [
                'name' => 'Head of School',
                'title' => "Principal's Address",
                'designation' => 'M.A., M.Ed., Head of School',
                'experience' => '20+ Years of Academic Leadership',
                'quote' => "Education is not merely about accumulating facts; it is about kindling an insatiable appetite for learning, shaping compassionate character, and instilling courage to lead with responsibility. At {$name}, every teacher is a mentor, and every child's dream is treated with sacred importance.",
            ],
            'stats' => [
                'stat1_number' => '450+',
                'stat1_label' => 'Enrolled Students',
                'stat2_number' => '32+',
                'stat2_label' => 'Expert Educators',
                'stat3_number' => '100%',
                'stat3_label' => 'Board Pass Rate',
                'stat4_number' => '25+',
                'stat4_label' => 'Years of Legacy',
            ],
            'facilities' => [
                'items' => [
                    [
                        'id' => 1,
                        'title' => 'Rich Digital Library',
                        'icon' => 'bi-book',
                        'color' => 'primary',
                        'description' => 'Over 10,000+ reference volumes, multimedia periodicals, and digital research workstations.',
                    ],
                    [
                        'id' => 2,
                        'title' => 'Modern STEM Labs',
                        'icon' => 'bi-pc-display',
                        'color' => 'success',
                        'description' => 'Air-conditioned computer laboratories, interactive robotics kits, and fully equipped physics, chemistry, biology labs.',
                    ],
                    [
                        'id' => 3,
                        'title' => 'Safe Transport Fleet',
                        'icon' => 'bi-bus-front',
                        'color' => 'danger',
                        'description' => 'GPS-monitored school buses covering major city routes with certified attendants and CCTV surveillance.',
                    ],
                    [
                        'id' => 4,
                        'title' => 'Residential Hostel',
                        'icon' => 'bi-building',
                        'color' => 'info',
                        'description' => 'Hygienic boarding dormitories with round-the-clock wardens, study sessions, and healthy dining.',
                    ],
                    [
                        'id' => 5,
                        'title' => 'Sports Complex',
                        'icon' => 'bi-dribbble',
                        'color' => 'warning',
                        'description' => 'Dedicated football field, indoor basketball and badminton courts, table tennis, and athletics track.',
                    ],
                    [
                        'id' => 6,
                        'title' => 'Parent Portal Access',
                        'icon' => 'bi-shield-lock',
                        'color' => 'secondary',
                        'description' => 'Live mobile and web access for parents to review attendance, report cards, fees, and circulars.',
                    ],
                ]
            ],
            'admissions' => [
                'badge' => 'Admissions 2026-27',
                'title' => 'Admission Procedure & Guidelines',
                'subtitle' => 'Join our vibrant academic community. Easy, transparent, and digital admission steps.',
                'documents' => [
                    'Birth Certificate: Municipal Corporation / Hospital certified copy.',
                    'Passport-size Photographs: 4 recent color photographs of the student.',
                    'Transfer Certificate (TC): Original counter-signed TC from previous school.',
                    'Previous Report Card: Marksheet or progress report from the previous class.',
                    'Identity & Address Proof: Aadhaar Card, Passport, or Utility Bill copy.',
                ]
            ],
        ];
    }

    protected function isSuperAdmin(?Request $request = null): bool
    {
        $user = $request->user();
        if (!$user) return false;
        if ($user->user_type === 'super_admin') return true;
        if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) return true;
        if ($user->roles && $user->roles->contains('name', 'Super Admin')) return true;
        return false;
    }

    protected function getSchoolId(Request $request): int
    {
        $isSuper = $this->isSuperAdmin($request);
        $explicit = $request->input('school_id') ?? $request->query('school_id') ?? $request->header('X-School-Id');

        if ($explicit !== null && $explicit !== '' && is_numeric($explicit) && (int)$explicit > 0) {
            $requestedId = (int)$explicit;
            if ($isSuper) {
                return $requestedId;
            }
            if ($request->user()?->school_id && (int)$request->user()->school_id === $requestedId) {
                return $requestedId;
            }
        }

        $tenantId = app(\App\Services\TenantManager::class)->getTenantId();
        if ($tenantId) {
            return (int) $tenantId;
        }

        if ($request->user()?->school_id) {
            return (int) $request->user()->school_id;
        }

        return (int) (School::first()?->id ?? 1);
    }

    /**
     * Get all website contents for admin CMS
     */
    public function getAll(Request $request)
    {
        $schoolId = $this->getSchoolId($request);
        $school = School::find($schoolId);
        if (!$school) {
            $school = School::first();
            $schoolId = $school ? $school->id : 1;
        }
        $defaults = self::getDefaultSettings($school);

        $savedSettings = WebsiteSetting::withoutGlobalScopes()->where('school_id', $schoolId)->get()->keyBy('section_key');

        $result = [];
        foreach ($defaults as $key => $defaultContent) {
            if ($savedSettings->has($key)) {
                $setting = $savedSettings->get($key);
                $result[$key] = array_merge($defaultContent, (array)$setting->content);
            } else {
                $result[$key] = $defaultContent;
            }
        }

        $isSuper = $this->isSuperAdmin($request);
        $allSchools = $isSuper 
            ? School::select('id', 'name', 'code', 'city', 'state', 'logo', 'subscription_plan')->orderBy('name')->get() 
            : [];

        return response()->json([
            'success' => true,
            'is_super_admin' => $isSuper,
            'all_schools' => $allSchools,
            'school' => $school ? [
                'id' => $school->id,
                'name' => $school->name,
                'code' => $school->code,
                'city' => $school->city,
                'state' => $school->state,
                'subscription_plan' => $school->subscription_plan,
                'live_url' => "/s/{$school->code}",
            ] : null,
            'data' => $result,
        ]);
    }

    /**
     * Update a specific website content section
     */
    public function updateSection(Request $request, $sectionKey)
    {
        $schoolId = $this->getSchoolId($request);
        $school = School::find($schoolId);
        $defaults = self::getDefaultSettings($school);

        if (!array_key_exists($sectionKey, $defaults)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid website section: {$sectionKey}",
            ], 422);
        }

        $validated = $request->validate([
            'content' => 'required|array',
        ]);

        $setting = WebsiteSetting::withoutGlobalScopes()->updateOrCreate(
            [
                'school_id' => $schoolId,
                'section_key' => $sectionKey,
            ],
            [
                'content' => $validated['content'],
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => ucfirst($sectionKey) . ' section updated successfully!',
            'data' => $setting->content,
        ]);
    }

    /**
     * Reset a section back to defaults
     */
    public function resetSection(Request $request, $sectionKey)
    {
        $schoolId = $this->getSchoolId($request);
        $school = School::find($schoolId);
        $defaults = self::getDefaultSettings($school);

        if (!array_key_exists($sectionKey, $defaults)) {
            return response()->json(['success' => false, 'message' => 'Invalid section.'], 422);
        }

        WebsiteSetting::withoutGlobalScopes()->where('school_id', $schoolId)->where('section_key', $sectionKey)->delete();

        return response()->json([
            'success' => true,
            'message' => ucfirst($sectionKey) . ' section reset to defaults.',
            'data' => $defaults[$sectionKey],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Notice Board Management
    |--------------------------------------------------------------------------
    */

    public function noticesIndex(Request $request)
    {
        $schoolId = $this->getSchoolId($request);

        $query = Notice::withoutGlobalScopes()
            ->with('creator:id,name')
            ->where('school_id', $schoolId);

        if ($request->filled('audience')) {
            $query->where('target_audience', $request->audience);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $notices = $query->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $notices,
        ]);
    }

    public function noticesStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'target_audience' => 'required|in:all,public,students,parents,teachers',
            'is_pinned' => 'boolean',
            'status' => 'boolean',
        ]);

        $schoolId = $this->getSchoolId($request);
        $validated['school_id'] = $schoolId;
        $validated['slug'] = Str::slug($validated['title']) . '-' . rand(1000, 9999);
        $validated['created_by'] = $request->user()->id ?? null;

        $notice = Notice::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notice published successfully!',
            'data' => $notice,
        ], 201);
    }

    public function noticesUpdate(Request $request, $id)
    {
        $schoolId = $this->getSchoolId($request);
        $notice = Notice::withoutGlobalScopes()->where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'target_audience' => 'required|in:all,public,students,parents,teachers',
            'is_pinned' => 'boolean',
            'status' => 'boolean',
        ]);

        $notice->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notice updated successfully!',
            'data' => $notice,
        ]);
    }

    public function noticesDestroy(Request $request, $id)
    {
        $schoolId = $this->getSchoolId($request);
        $notice = Notice::withoutGlobalScopes()->where('school_id', $schoolId)->findOrFail($id);
        $notice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notice deleted successfully.',
        ]);
    }
}
