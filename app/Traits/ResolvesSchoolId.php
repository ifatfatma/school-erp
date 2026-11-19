<?php

namespace App\Traits;

use App\Models\School;
use App\Services\TenantManager;
use Illuminate\Http\Request;

trait ResolvesSchoolId
{
    /**
     * Check if user is Super Admin.
     */
    protected function isSuperAdmin(?Request $request = null): bool
    {
        $user = $request ? $request->user() : auth()->user();
        if (!$user) {
            return false;
        }

        return ($user->user_type ?? '') === 'super_admin' || 
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))) ||
            (!empty($user->roles) && $user->roles->contains('name', 'Super Admin'));
    }

    /**
     * Resolve active school ID for current context.
     */
    protected function resolveSchoolId(?Request $request = null): ?int
    {
        $req = $request ?? (function_exists('request') ? request() : null);
        $user = $req ? $req->user() : auth()->user();

        // 1. Check TenantManager active school (handles Super Admin & authorized multi-school admins)
        try {
            $tenantManager = app(TenantManager::class);
            $tenantId = $tenantManager->getTenantId();
            if ($tenantId) {
                return (int) $tenantId;
            }
        } catch (\Throwable $e) {}

        // 2. Explicit request parameter or header if authorized
        if ($req) {
            $inputSchool = $req->input('school_id') ?? $req->query('school_id') ?? $req->header('X-School-Id');
            if ($inputSchool !== null && $inputSchool !== '' && $inputSchool !== 'all' && $inputSchool !== '0') {
                $targetId = (int) $inputSchool;
                if ($this->isSuperAdmin($req) || ($user && $user->canAccessSchool($targetId))) {
                    return $targetId;
                }
            }
        }

        // 3. Fallback to user's assigned school
        if (!empty($user?->school_id)) {
            return (int) $user->school_id;
        }

        return null;
    }
}
