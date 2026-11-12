<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\OnlineAdmission;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\WebsiteInquiry;
use App\Models\WebsiteSetting;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicAdmissionController extends Controller
{
    /**
     * Resolve the requested School / Tenant from query, route, header, or subdomain
     */
    protected function resolveSchool(Request $request): School
    {
        $identifier = $request->input('school')
            ?? $request->input('school_code')
            ?? $request->input('code')
            ?? $request->input('tenant')
            ?? $request->route('school')
            ?? $request->route('schoolCode')
            ?? $request->header('X-School-Code')
            ?? $request->header('X-School-Id');

        if ($identifier) {
            $school = School::where('status', true)
                ->where(function ($q) use ($identifier) {
                    $q->where('code', $identifier)
                      ->orWhere('id', $identifier);
                })
                ->first();

            if ($school) {
                return $school;
            }
        }

        // Check subdomain (e.g. s002.example.com)
        $host = $request->getHost();
        $parts = explode('.', $host);
        if (count($parts) > 1 && !in_array($parts[0], ['www', 'localhost', '127', 'admin'])) {
            $sub = $parts[0];
            $school = School::where('status', true)
                ->where(function ($q) use ($sub) {
                    $q->where('code', $sub)
                      ->orWhere('name', 'LIKE', "%{$sub}%");
                })
                ->first();

            if ($school) {
                return $school;
            }
        }

        return School::where('status', true)->first() ?? School::first();
    }

    /**
     * Get dynamic website content and notices for the requested school
     */
    public function getWebsiteContent(Request $request)
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school ? $school->id : 1;

        $defaults = WebsiteManagerController::getDefaultSettings($school);
        $saved = WebsiteSetting::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->get()
            ->keyBy('section_key');

        $content = [];
        foreach ($defaults as $key => $defaultVal) {
            if ($saved->has($key)) {
                $content[$key] = array_merge($defaultVal, (array)$saved->get($key)->content);
            } else {
                $content[$key] = $defaultVal;
            }
        }

        // Active public notices for this school
        $notices = Notice::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', true)
            ->whereIn('target_audience', ['all', 'public'])
            ->where('publish_date', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->limit(10)
            ->get();

        // All active schools for public campus / school switcher
        $allSchools = School::where('status', true)
            ->get(['id', 'name', 'code', 'city', 'state', 'logo'])
            ->map(function ($s) {
                $logoUrl = null;
                if ($s->logo) {
                    $raw = is_array($s->logo) ? reset($s->logo) : $s->logo;
                    if ($raw && is_string($raw) && $raw !== '[]' && $raw !== 'null' && $raw !== '{}') {
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
                ];
            });

        $schoolLogo = null;
        if ($school && $school->logo) {
            $raw = is_array($school->logo) ? reset($school->logo) : $school->logo;
            if ($raw && is_string($raw) && $raw !== '[]' && $raw !== 'null' && $raw !== '{}') {
                $schoolLogo = str_starts_with($raw, 'http') ? $raw : asset('storage/' . ltrim($raw, '/'));
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'code' => $school->code,
                    'email' => $school->email,
                    'phone' => $school->phone,
                    'address' => $school->address,
                    'city' => $school->city,
                    'state' => $school->state,
                    'country' => $school->country,
                    'postal_code' => $school->postal_code,
                    'logo' => $schoolLogo,
                ],
                'content' => $content,
                'notices' => $notices,
                'all_schools' => $allSchools,
            ],
        ]);
    }

    /**
     * Public metadata for website and admission form
     */
    public function metadata(Request $request)
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school ? $school->id : 1;

        $sessions = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get(['id', 'name', 'start_date', 'end_date', 'status']);

        $classes = SchoolClass::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with(['sections' => function ($q) use ($schoolId) {
                $q->withoutGlobalScopes()->where('school_id', $schoolId)->select(['id', 'class_id', 'name']);
            }])
            ->orderBy('numeric_name', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'numeric_name']);

        $allSchools = School::where('status', true)
            ->select('id', 'name', 'code', 'city', 'state')
            ->get();

        return response()->json([
            'success' => true,
            'school' => [
                'id' => $school->id,
                'name' => $school->name,
                'code' => $school->code,
            ],
            'sessions' => $sessions,
            'classes' => $classes,
            'all_schools' => $allSchools,
        ]);
    }

    /**
     * Submit online admission application
     */
    public function apply(Request $request)
    {
        $validated = $request->validate([
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'class_id' => 'required|exists:classes,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'religion' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
            'aadhaar_no' => 'nullable|string|max:20',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'required|string|max:500',
            'father_name' => 'required|string|max:100',
            'father_mobile' => 'nullable|string|max:20',
            'father_occupation' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'mother_mobile' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_mobile' => 'nullable|string|max:20',
            'previous_school' => 'nullable|string|max:255',
            'previous_class' => 'nullable|string|max:50',
            'previous_marks' => 'nullable|string|max:50',
            'school' => 'nullable|string',
            'school_code' => 'nullable|string',
        ]);

        $school = $this->resolveSchool($request);
        $schoolId = $school ? $school->id : 1;

        // Auto determine session if not provided
        if (empty($validated['academic_session_id'])) {
            $activeSession = AcademicSession::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('status', 'active')
                ->first();
            $validated['academic_session_id'] = $activeSession ? $activeSession->id : null;
        }

        // Generate unique application number ADM-YYYY-XXXX
        $year = date('Y');
        $countThisYear = OnlineAdmission::withoutGlobalScopes()->whereYear('created_at', $year)->count() + 1;
        $appNo = sprintf('ADM-%s-%04d', $year, $countThisYear);

        // Ensure uniqueness
        while (OnlineAdmission::withoutGlobalScopes()->where('application_no', $appNo)->exists()) {
            $countThisYear++;
            $appNo = sprintf('ADM-%s-%04d', $year, $countThisYear);
        }

        $validated['school_id'] = $schoolId;
        $validated['application_no'] = $appNo;
        $validated['status'] = 'pending';

        $application = OnlineAdmission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Admission application submitted successfully!',
            'application_no' => $application->application_no,
            'school_name' => $school->name,
            'data' => $application,
        ], 201);
    }

    /**
     * Track admission status by application_no and mobile
     */
    public function track(Request $request)
    {
        $request->validate([
            'application_no' => 'required|string',
            'mobile' => 'nullable|string',
        ]);

        $query = OnlineAdmission::withoutGlobalScopes()
            ->with(['class:id,name', 'academicSession:id,name', 'school:id,name,code'])
            ->where('application_no', trim($request->application_no));

        if ($request->filled('mobile')) {
            $query->where('mobile', trim($request->mobile));
        }

        $application = $query->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'No application found with the provided details.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'application_no' => $application->application_no,
                'full_name' => $application->full_name,
                'school_name' => $application->school ? $application->school->name : null,
                'class_name' => $application->class ? $application->class->name : 'N/A',
                'session_name' => $application->academicSession ? $application->academicSession->name : 'Current',
                'submission_date' => $application->created_at->format('d M, Y'),
                'status' => $application->status,
                'admin_notes' => $application->admin_notes,
                'student_id' => $application->student_id,
            ],
        ]);
    }

    /**
     * Submit contact form inquiry
     */
    public function submitInquiry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:1000',
            'school' => 'nullable|string',
            'school_code' => 'nullable|string',
        ]);

        $school = $this->resolveSchool($request);
        $validated['school_id'] = $school ? $school->id : 1;
        $validated['status'] = 'unread';

        $inquiry = WebsiteInquiry::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Thank you for reaching out to {$school->name}! We have received your message and will get back to you soon.",
            'data' => $inquiry,
        ], 201);
    }

    /**
     * Public stats for website home page
     */
    public function stats(Request $request)
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school ? $school->id : 1;

        $totalStudents = Student::withoutGlobalScopes()->where('school_id', $schoolId)->where('status', true)->count();
        $totalTeachers = Teacher::withoutGlobalScopes()->where('school_id', $schoolId)->where('status', true)->count();
        $totalClasses = SchoolClass::withoutGlobalScopes()->where('school_id', $schoolId)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'code' => $school->code,
                ],
                'total_students' => $totalStudents > 0 ? $totalStudents : 350,
                'total_teachers' => $totalTeachers > 0 ? $totalTeachers : 28,
                'total_classes' => $totalClasses > 0 ? $totalClasses : 10,
                'pass_rate' => '99.5%',
                'experience_years' => 20,
            ],
        ]);
    }
}
