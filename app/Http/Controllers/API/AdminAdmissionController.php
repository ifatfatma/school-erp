<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\OnlineAdmission;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\WebsiteInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAdmissionController extends Controller
{
    protected function isSuperAdmin(?Request $request = null): bool
    {
        $user = $request->user();
        if (!$user) return false;
        if ($user->user_type === 'super_admin') return true;
        if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) return true;
        if ($user->roles && $user->roles->contains('name', 'Super Admin')) return true;
        return false;
    }

    protected function resolveSchoolId(?Request $request = null): ?int
    {
        $isSuper = $this->isSuperAdmin($request);

        // 1. Explicit school_id passed via query / body / header
        $explicit = $request->input('school_id') ?? $request->query('school_id') ?? $request->header('X-School-Id');
        if ($explicit !== null && $explicit !== '') {
            if ($explicit === 'all' || $explicit === '0') {
                return $isSuper ? null : (int) $request->user()?->school_id;
            }
            if (is_numeric($explicit) && (int)$explicit > 0) {
                return $isSuper ? (int) $explicit : (int) $request->user()?->school_id;
            }
        }

        // 2. TenantManager context
        $tenantId = app(\App\Services\TenantManager::class)->getTenantId();
        if ($tenantId) {
            return (int) $tenantId;
        }

        // 3. User's assigned school
        if ($request->user()?->school_id) {
            return (int) $request->user()->school_id;
        }

        // 4. For Super Admin with no filter, return null (view all schools)
        if ($isSuper) {
            return null;
        }

        return (int) (School::first()?->id ?? 1);
    }

    /**
     * List all online admission applications
     */
    public function index(Request $request)
    {
        $isSuper = $this->isSuperAdmin($request);
        $schoolId = $this->resolveSchoolId($request);

        $query = OnlineAdmission::withoutGlobalScopes()->with([
            'school:id,name,code',
            'class:id,name',
            'academicSession:id,name',
            'student:id,admission_no,first_name,last_name'
        ]);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('application_no', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $applications = $query->orderBy('id', 'desc')->paginate($perPage);

        // Compute summary counts
        $countsQuery = OnlineAdmission::withoutGlobalScopes();
        if ($schoolId) {
            $countsQuery->where('school_id', $schoolId);
        }

        $counts = [
            'all' => (clone $countsQuery)->count(),
            'pending' => (clone $countsQuery)->where('status', 'pending')->count(),
            'under_review' => (clone $countsQuery)->where('status', 'under_review')->count(),
            'approved' => (clone $countsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $countsQuery)->where('status', 'rejected')->count(),
            'enrolled' => (clone $countsQuery)->where('status', 'enrolled')->count(),
        ];

        // Also fetch classes for filter (either school-specific or all)
        $classesQuery = SchoolClass::withoutGlobalScopes();
        if ($schoolId) {
            $classesQuery->where('school_id', $schoolId);
        }
        $classes = $classesQuery->select('id', 'name')->orderBy('id')->get();

        $allSchools = $isSuper 
            ? School::select('id', 'name', 'code', 'city', 'state')->orderBy('name')->get() 
            : [];

        return response()->json([
            'success' => true,
            'is_super_admin' => $isSuper,
            'selected_school_id' => $schoolId,
            'all_schools' => $allSchools,
            'classes' => $classes,
            'data' => $applications,
            'counts' => $counts,
        ]);
    }

    /**
     * Show single application
     */
    public function show(Request $request, $id)
    {
        $isSuper = $this->isSuperAdmin($request);
        $query = OnlineAdmission::withoutGlobalScopes()->with([
            'school:id,name,code,city,state',
            'class:id,name',
            'academicSession:id,name',
            'student'
        ]);

        if (!$isSuper && $request->user()?->school_id) {
            $query->where('school_id', $request->user()->school_id);
        }

        $application = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $application,
        ]);
    }

    /**
     * Update application status and admin notes
     */
    public function updateStatus(Request $request, $id)
    {
        $isSuper = $this->isSuperAdmin($request);
        $query = OnlineAdmission::withoutGlobalScopes();
        if (!$isSuper && $request->user()?->school_id) {
            $query->where('school_id', $request->user()->school_id);
        }

        $application = $query->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,enrolled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Application status updated to {$application->status}.",
            'data' => $application,
        ]);
    }

    /**
     * Approve and automatically enroll applicant as a student in ERP
     */
    public function enrollStudent(Request $request, $id)
    {
        $isSuper = $this->isSuperAdmin($request);
        $query = OnlineAdmission::withoutGlobalScopes();
        if (!$isSuper && $request->user()?->school_id) {
            $query->where('school_id', $request->user()->school_id);
        }

        $application = $query->findOrFail($id);
        $schoolId = (int) $application->school_id;

        if ($application->status === 'enrolled' && $application->student_id) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant is already enrolled as a student.',
            ], 422);
        }

        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'roll_no' => 'nullable|string|max:20',
            'admission_date' => 'nullable|date',
            'create_user_account' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($application, $request, $schoolId) {
            // Next admission number
            $latestStudent = Student::withoutGlobalScopes()->where('school_id', $schoolId)->latest('id')->first();
            $nextNum = ($latestStudent ? $latestStudent->id : 0) + 1;
            $admissionNo = sprintf('STU%s%04d', date('Y'), $nextNum);

            // Ensure unique admission_no
            while (Student::withoutGlobalScopes()->where('school_id', $schoolId)->where('admission_no', $admissionNo)->exists()) {
                $nextNum++;
                $admissionNo = sprintf('STU%s%04d', date('Y'), $nextNum);
            }

            // Create student record
            $student = Student::create([
                'school_id' => $schoolId,
                'academic_session_id' => $application->academic_session_id ?? AcademicSession::where('status', 'active')->value('id'),
                'class_id' => $application->class_id,
                'section_id' => $request->section_id,
                'admission_no' => $admissionNo,
                'roll_no' => $request->roll_no ?? (string)$nextNum,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'gender' => $application->gender,
                'dob' => $application->dob,
                'admission_date' => $request->admission_date ?? now()->toDateString(),
                'blood_group' => $application->blood_group,
                'religion' => $application->religion,
                'category' => $application->category,
                'aadhaar_no' => $application->aadhaar_no,
                'mobile' => $application->mobile,
                'email' => $application->email,
                'address' => $application->address,
                'father_name' => $application->father_name,
                'father_mobile' => $application->father_mobile,
                'father_occupation' => $application->father_occupation,
                'mother_name' => $application->mother_name,
                'mother_mobile' => $application->mother_mobile,
                'guardian_name' => $application->guardian_name ?? $application->father_name,
                'guardian_mobile' => $application->guardian_mobile ?? $application->father_mobile ?? $application->mobile,
                'status' => true,
                'created_by' => $request->user()->id ?? null,
            ]);

            // Create user account for student portal if requested or by default
            if ($request->boolean('create_user_account', true)) {
                $userEmail = !empty($application->email) ? $application->email : strtolower($admissionNo) . '@school.com';
                $password = '12345678';

                // If user doesn't already exist with this email
                if (!User::where('email', $userEmail)->exists()) {
                    $user = User::create([
                        'school_id' => $schoolId,
                        'name' => $student->full_name,
                        'username' => strtolower($admissionNo),
                        'email' => $userEmail,
                        'phone' => $student->mobile,
                        'user_type' => 'student',
                        'password' => Hash::make($password),
                        'status' => 1,
                    ]);

                    $user->assignRole('Student');
                    $student->update(['user_id' => $user->id]);
                }
            }

            // Update application status
            $application->update([
                'status' => 'enrolled',
                'student_id' => $student->id,
                'admin_notes' => ($application->admin_notes ? $application->admin_notes . "\n" : '') . "Enrolled as Student #{$admissionNo} on " . now()->toFormattedDateString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Applicant successfully enrolled as Student #{$admissionNo}!",
                'student' => $student,
                'application' => $application,
            ]);
        });
    }

    /**
     * List website inquiries
     */
    public function inquiries(Request $request)
    {
        $isSuper = $this->isSuperAdmin($request);
        $schoolId = $this->resolveSchoolId($request);

        $query = WebsiteInquiry::withoutGlobalScopes()->with('school:id,name,code');

        if ($schoolId) {
            $query->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->orWhereNull('school_id');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $inquiries = $query->orderBy('id', 'desc')->paginate($perPage);

        $allSchools = $isSuper 
            ? School::select('id', 'name', 'code', 'city', 'state')->orderBy('name')->get() 
            : [];

        return response()->json([
            'success' => true,
            'is_super_admin' => $isSuper,
            'selected_school_id' => $schoolId,
            'all_schools' => $allSchools,
            'data' => $inquiries,
        ]);
    }

    /**
     * Update inquiry status
     */
    public function updateInquiryStatus(Request $request, $id)
    {
        $isSuper = $this->isSuperAdmin($request);
        $query = WebsiteInquiry::withoutGlobalScopes();
        if (!$isSuper && $request->user()?->school_id) {
            $query->where('school_id', $request->user()->school_id);
        }
        $inquiry = $query->findOrFail($id);

        $request->validate(['status' => 'required|in:unread,read,responded']);
        $inquiry->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry status updated successfully.',
            'data' => $inquiry,
        ]);
    }

    /**
     * Delete inquiry
     */
    public function deleteInquiry(Request $request, $id)
    {
        $isSuper = $this->isSuperAdmin($request);
        $query = WebsiteInquiry::withoutGlobalScopes();
        if (!$isSuper && $request->user()?->school_id) {
            $query->where('school_id', $request->user()->school_id);
        }
        $inquiry = $query->findOrFail($id);
        $inquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inquiry deleted successfully.',
        ]);
    }
}
