<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolResource;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Get list of active schools for the switcher.
     * Super Admin gets all schools; multi-school admins get their assigned schools.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $isSuperAdmin = $this->tenantManager->isSuperAdmin($user);

        if ($isSuperAdmin) {
            $schools = School::where('status', true)->orderBy('name')->get();
        } else {
            $schools = $user ? $user->getAssignedSchools() : collect();
        }

        $maxSchools = $user ? $user->maxAllowedSchools() : 1;
        $canCreateSchool = $isSuperAdmin || ($user && $user->canAddMoreSchools());
        $canSwitch = $isSuperAdmin || ($maxSchools > 1 && $schools->count() > 1);

        return response()->json([
            'success' => true,
            'data'    => SchoolResource::collection($schools),
            'is_super_admin' => $isSuperAdmin,
            'can_create_school' => $canCreateSchool,
            'can_switch' => $canSwitch,
            'total_schools' => $schools->count(),
            'max_schools' => $maxSchools,
            'owned_count' => $schools->count(),
            'active_plan' => $user?->subscription?->plan?->name ?? 'Starter / Basic Plan',
            'active_plan_slug' => $user?->subscription?->plan?->slug ?? 'basic',
        ]);
    }

    /**
     * Get the currently active tenant details.
     */
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();
        $isSuperAdmin = $this->tenantManager->isSuperAdmin($user);
        $tenantId = $this->tenantManager->getTenantId();

        if ($tenantId === null) {
            if (!$isSuperAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No school tenant assigned to this account.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'is_global' => true,
                'is_super_admin' => true,
                'message' => 'Global Mode (All Schools)',
                'tenant' => null,
            ]);
        }

        $school = School::find($tenantId);

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant school not found.',
            ], 404);
        }

        // Security check: ensure user has access to this tenant school
        if (!$isSuperAdmin && !$user->canAccessSchool($school->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this school.',
            ], 403);
        }

        // Gather tenant counts
        $studentsCount = Student::where('school_id', $school->id)->count();
        $teachersCount = Teacher::where('school_id', $school->id)->count();
        $usersCount = User::where('school_id', $school->id)->count();

        return response()->json([
            'success' => true,
            'is_global' => false,
            'is_super_admin' => $isSuperAdmin,
            'tenant' => new SchoolResource($school),
            'stats' => [
                'students' => $studentsCount,
                'teachers' => $teachersCount,
                'users'    => $usersCount,
            ],
        ]);
    }

    /**
     * Switch active tenant context by body input.
     */
    public function switch(Request $request): JsonResponse
    {
        return $this->processSwitch($request->input('school_id'), $request);
    }

    /**
     * Switch active tenant context by URL param.
     */
    public function switchByParam($school, Request $request): JsonResponse
    {
        $schoolId = is_numeric($school) ? (int) $school : $school;
        return $this->processSwitch($schoolId, $request);
    }

    /**
     * Core switch processing logic.
     * Accessible by Super Admins and School Admins with assigned schools.
     */
    protected function processSwitch($schoolId, Request $request): JsonResponse
    {
        $user = $request->user();
        $isSuperAdmin = $this->tenantManager->isSuperAdmin($user);

        // Global mode (all schools) is restricted to Super Admin only
        if ($schoolId === 'all' || $schoolId === '0' || $schoolId === null || $schoolId === '') {
            if (!$isSuperAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Global mode is restricted to Super Administrators.',
                ], 403);
            }

            $this->tenantManager->setTenantId(null);
            session()->forget('active_school_id');

            return response()->json([
                'success' => true,
                'message' => 'Switched to Global Mode (All Schools).',
                'active_school_id' => 'all',
                'is_global' => true,
                'tenant' => null,
            ]);
        }

        $targetSchoolId = (int) $schoolId;

        // Security check: Super Admin or user explicitly authorized for this school
        if (!$isSuperAdmin && !$user->canAccessSchool($targetSchoolId)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have access to this school.',
            ], 403);
        }

        $school = School::find($targetSchoolId);

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School not found.',
            ], 404);
        }

        if (!$school->status) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot switch to an inactive school.',
            ], 422);
        }

        $this->tenantManager->setTenantId((int) $school->id);
        session(['active_school_id' => $school->id]);

        return response()->json([
            'success' => true,
            'message' => "Switched to {$school->name}.",
            'active_school_id' => (string) $school->id,
            'is_global' => false,
            'tenant' => new SchoolResource($school),
        ]);
    }

    /**
     * Multi-tenant system overview for Super Admin.
     */
    public function systemOverview(Request $request): JsonResponse
    {
        if (!$this->tenantManager->isSuperAdmin($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $totalSchools = School::count();
        $activeSchools = School::where('status', true)->count();
        $totalStudents = Student::withoutSchoolScope()->count();
        $totalTeachers = Teacher::withoutSchoolScope()->count();
        $totalUsers = User::withoutSchoolScope()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_schools'  => $totalSchools,
                'active_schools' => $activeSchools,
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'total_users'    => $totalUsers,
            ],
        ]);
    }
}
