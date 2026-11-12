<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TenantManager
{
    protected ?int $tenantId = null;
    protected ?School $tenant = null;
    protected bool $isOverridden = false;

    /**
     * Set the current tenant ID.
     */
    public function setTenantId(?int $id): self
    {
        $this->tenantId = $id;
        $this->tenant = null;
        $this->isOverridden = true;
        return $this;
    }

    /**
     * Clear the tenant override.
     */
    public function clearOverride(): self
    {
        $this->isOverridden = false;
        $this->tenantId = null;
        $this->tenant = null;
        return $this;
    }

    /**
     * Get the active tenant ID.
     */
    public function getTenantId(): ?int
    {
        if ($this->isOverridden) {
            return $this->tenantId;
        }

        $user = Auth::user();

        if ($user) {
            // Check if Super Admin provided an active school header/query
            if ($this->isSuperAdmin($user)) {
                $requested = request()->header('X-School-Id') 
                    ?? request()->query('tenant_school_id')
                    ?? session('active_school_id');

                if ($requested !== null && $requested !== '') {
                    if ($requested === 'all' || $requested === '0') {
                        return null; // Global mode (no tenant restriction)
                    }
                    return (int) $requested;
                }

                // If Super Admin hasn't specified an active school, return null (Global Mode)
                return null;
            }

            // Normal users (School Admin, Teacher, Student, Parent): ALWAYS strictly their assigned school_id
            return $user->school_id ? (int) $user->school_id : null;
        }

        return null;
    }

    /**
     * Get the active School model.
     */
    public function getTenant(): ?School
    {
        $id = $this->getTenantId();

        if (!$id) {
            return null;
        }

        if ($this->tenant && $this->tenant->id === $id) {
            return $this->tenant;
        }

        return $this->tenant = School::find($id);
    }

    /**
     * Check if a specific tenant is active.
     */
    public function hasTenant(): bool
    {
        return !is_null($this->getTenantId());
    }

    /**
     * Check if the authenticated user is a Super Admin.
     */
    public function isSuperAdmin(?User $user = null): bool
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return false;
        }

        if (method_exists($user, 'isSuperAdmin')) {
            return $user->isSuperAdmin();
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin', 'sanctum') || $user->hasRole('Super Admin'))) {
            return true;
        }

        return in_array(strtolower((string) ($user->user_type ?? '')), ['super_admin', 'super admin'], true);
    }

    /**
     * Temporarily execute a callback within the context of a specific tenant.
     */
    public function runAs(?int $tenantId, callable $callback)
    {
        $prevId = $this->tenantId;
        $prevOverridden = $this->isOverridden;

        $this->setTenantId($tenantId);

        try {
            return $callback();
        } finally {
            $this->tenantId = $prevId;
            $this->isOverridden = $prevOverridden;
            $this->tenant = null;
        }
    }
}
