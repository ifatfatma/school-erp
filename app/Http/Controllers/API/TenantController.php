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
     * Get list of active schools for the switcher (Super Admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$this->tenantManager->isSuperAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Super Admin can access the school switcher.',
            ], 403);
        }

        $schools = School::where('status', true)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data'    => SchoolResource::collection($schools),
            'is_super_admin' => true,
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
     * Switch active tenant context by body input (Super Admin only).
     */
    public function switch(Request $request): JsonResponse
    {
        return $this->processSwitch($request->input('school_id'), $request);
    }

    /**
     * Switch active tenant context by URL param (Super Admin only).
     */
    public function switchByParam($school, Request $request): JsonResponse
    {
        $schoolId = is_numeric($school) ? (int) $school : $school;
        return $this->processSwitch($schoolId, $request);
    }

    /**
     * Core switch processing logic.
     */
    protected function processSwitch($schoolId, Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$this->tenantManager->isSuperAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Super Admin can switch schools.',
            ], 403);
        }

        if ($schoolId === 'all' || $schoolId === '0' || $schoolId === null || $schoolId === '') {
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

        $school = School::find($schoolId);

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
