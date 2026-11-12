<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Whitelist logout so users can always terminate their session smoothly
        if ($request->is('api/logout') || $request->is('logout')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user) {
            if ($this->tenantManager->isSuperAdmin($user)) {
                // Super Admin: allow tenant switching via header or query
                $requestedSchool = $request->header('X-School-Id') 
                    ?? $request->query('tenant_school_id') 
                    ?? session('active_school_id');

                if ($requestedSchool !== null && $requestedSchool !== '') {
                    if ($requestedSchool === 'all' || $requestedSchool === '0') {
                        $this->tenantManager->setTenantId(null);
                    } else {
                        $school = School::find($requestedSchool);
                        if (!$school) {
                            return response()->json([
                                'success' => false,
                                'message' => 'The selected school was not found.',
                            ], 404);
                        }

                        if (!$school->status) {
                            return response()->json([
                                'success' => false,
                                'message' => 'The selected school is inactive and cannot be accessed.',
                            ], 422);
                        }

                        $this->tenantManager->setTenantId((int) $school->id);
                    }
                } else {
                    // Default for Super Admin if no active selection is made is Global Mode
                    $this->tenantManager->setTenantId(null);
                }
            } else {
                // Non-Super Admin (School Admin, Teacher, Student, Parent)
                // 1. Ensure user has an assigned school
                if (empty($user->school_id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied: No school is assigned to this account.',
                    ], 403);
                }

                // 2. Ensure the assigned school is active
                $school = School::find($user->school_id);
                if (!$school || !$school->status) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your school account is currently inactive. Please contact the platform administrator.',
                    ], 403);
                }

                // 3. Strictly lock active tenant to the user's assigned school.
                // Any client header is completely ignored for non-Super Admins, guaranteeing tenant isolation without fragile client header mismatches.
                $this->tenantManager->setTenantId((int) $user->school_id);

                // 4. Guard against request body/query school_id tampering
                if ($request->has('school_id')) {
                    $submittedSchoolId = $request->input('school_id');
                    if ($submittedSchoolId !== null && (string) $submittedSchoolId !== (string) $user->school_id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Access denied: You cannot assign records or operate on another school.',
                        ], 403);
                    }
                }
            }
        }

        $response = $next($request);

        // Attach tenant id header to response for frontend awareness
        $activeTenantId = $this->tenantManager->getTenantId();
        $response->headers->set('X-Active-Tenant-Id', $activeTenantId !== null ? (string) $activeTenantId : 'all');

        return $response;
    }
}
