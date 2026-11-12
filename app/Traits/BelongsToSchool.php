<?php

namespace App\Traits;

use App\Models\School;
use App\Scopes\SchoolScope;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSchool
{
    /**
     * Boot the BelongsToSchool trait.
     */
    public static function bootBelongsToSchool(): void
    {
        // Automatically scope all queries to the active tenant
        static::addGlobalScope(new SchoolScope);

        // Automatically assign active tenant ID when creating records
        static::creating(function ($model) {
            $user = auth()->user();
            $tenantManager = app(TenantManager::class);

            if ($user && !$tenantManager->isSuperAdmin($user)) {
                // Non-Super Admins are strictly bound to their assigned school
                $model->school_id = (int) $user->school_id;
            } else {
                if (empty($model->school_id)) {
                    $tenantId = $tenantManager->getTenantId();
                    if ($tenantId) {
                        $model->school_id = $tenantId;
                    } elseif (!empty($model->class_id)) {
                        $class = \App\Models\SchoolClass::withoutGlobalScopes()->find($model->class_id);
                        if ($class && $class->school_id) {
                            $model->school_id = $class->school_id;
                        }
                    } elseif ($user && $user->school_id) {
                        $model->school_id = (int) $user->school_id;
                    }
                    if (empty($model->school_id)) {
                        $model->school_id = \App\Models\School::first()?->id ?? 1;
                    }
                }
            }
        });

        // Prevent non-Super Admin from updating school_id to another school
        static::updating(function ($model) {
            $user = auth()->user();
            $tenantManager = app(TenantManager::class);

            if ($user && !$tenantManager->isSuperAdmin($user)) {
                if ($model->isDirty('school_id') && (string) $model->school_id !== (string) $user->school_id) {
                    abort(403, 'Unauthorized school modification.');
                }
            }
        });
    }

    /**
     * Relationship to the School tenant.
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Query records specifically for a given school.
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->withoutGlobalScope(SchoolScope::class)
            ->where($this->getTable() . '.school_id', $schoolId);
    }

    /**
     * Query records without the tenant global scope.
     */
    public function scopeWithoutSchoolScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(SchoolScope::class);
    }
}
