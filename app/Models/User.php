<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, BelongsToSchool;

    protected $guard_name = 'sanctum';

    protected $fillable = [
        'school_id', 
        'name', 
        'username', 
        'email', 
        'phone', 
        'user_type', 
        'email_verified_at', 
        'password', 
        'status', 
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Schools assigned to this user (for multi-school administrators).
     */
    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_user')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Schools owned/managed by this user via school_user pivot.
     */
    public function ownedSchools()
    {
        return $this->belongsToMany(School::class, 'school_user')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Active subscription for this user.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Invoices for this user.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class)->latest();
    }

    /**
     * Maximum number of schools allowed under the user's active plan.
     */
    public function maxAllowedSchools(): int
    {
        $sub = $this->subscription;
        if ($sub && $sub->plan) {
            return (int) $sub->plan->max_schools;
        }

        // Fallback to primary school's plan if no explicit subscription record
        $schoolPlan = strtolower($this->school?->subscription_plan ?? 'basic');
        $plan = Plan::where('slug', $schoolPlan)->first();
        if ($plan) {
            return (int) $plan->max_schools;
        }

        return match ($schoolPlan) {
            'growth', 'professional' => 2,
            'enterprise' => 5,
            default => 1,
        };
    }

    /**
     * Check if user is eligible to add more schools under their plan.
     */
    public function canAddMoreSchools(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->ownedSchools()->count() < $this->maxAllowedSchools();
    }

    /**
     * Automatically synchronize Spatie school.manage_multiple permission based on maxAllowedSchools.
     */
    public function syncSchoolPermissionsBasedOnPlan(): void
    {
        if ($this->isSuperAdmin()) {
            return;
        }

        $max = $this->maxAllowedSchools();
        try {
            if ($max > 1) {
                if (!$this->hasPermissionTo('school.manage_multiple', 'sanctum')) {
                    $this->givePermissionTo('school.manage_multiple');
                }
            } else {
                if ($this->hasPermissionTo('school.manage_multiple', 'sanctum') || $this->hasPermissionTo('school.manage_multiple')) {
                    $this->revokePermissionTo('school.manage_multiple');
                }
            }
        } catch (\Throwable $e) {
            // Permission cache or not yet seeded
        }
    }

    /**
     * Check if user can access a specific school.
     */
    public function canAccessSchool($schoolId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($schoolId === null || $schoolId === '' || $schoolId === 'all' || $schoolId === 0 || $schoolId === '0') {
            return false;
        }

        $targetId = (int) $schoolId;

        if ((int) $this->school_id === $targetId) {
            return true;
        }

        return $this->ownedSchools()->where('schools.id', $targetId)->exists();
    }

    /**
     * Check if user has permission to add/manage multiple schools.
     */
    public function canManageMultipleSchools(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Strictly enforce max_schools limit: Basic (1) can never manage multiple
        if ($this->maxAllowedSchools() <= 1) {
            return false;
        }

        try {
            return $this->hasPermissionTo('school.manage_multiple', 'sanctum') || 
                   $this->hasPermissionTo('school.manage_multiple');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get list of all schools assigned to this user.
     */
    public function getAssignedSchools()
    {
        if ($this->isSuperAdmin()) {
            return School::where('status', true)->orderBy('name')->get();
        }

        $schools = $this->ownedSchools()->where('status', true)->orderBy('name')->get();

        if ($this->school && !$schools->contains('id', $this->school_id)) {
            $schools->push($this->school);
        }

        return $schools;
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function guardian()
    {
        return $this->hasOne(Guardian::class);
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        if (in_array(strtolower((string) $this->user_type), ['super_admin', 'super admin'], true)) {
            return true;
        }

        return $this->hasRole('Super Admin', 'sanctum') || $this->hasRole('Super Admin');
    }

    /**
     * Check if user is School Admin.
     */
    public function isSchoolAdmin(): bool
    {
        if (in_array(strtolower((string) $this->user_type), ['school_admin', 'admin'], true)) {
            return true;
        }

        return $this->hasRole('School Admin', 'sanctum') || $this->hasRole('School Admin');
    }
}