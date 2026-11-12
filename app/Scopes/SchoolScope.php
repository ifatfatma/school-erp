<?php

namespace App\Scopes;

use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class SchoolScope implements Scope
{
    /**
     * Cache table school_id column checks to avoid repeated schema lookups.
     */
    protected static array $hasSchoolIdColumn = [];

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();

        if (!array_key_exists($table, static::$hasSchoolIdColumn)) {
            static::$hasSchoolIdColumn[$table] = Schema::hasColumn($table, 'school_id');
        }

        if (!static::$hasSchoolIdColumn[$table]) {
            return;
        }

        $tenantManager = app(TenantManager::class);

        $tenantId = $tenantManager->getTenantId();

        if ($tenantId !== null) {
            $builder->where($table . '.school_id', $tenantId);
        }
    }
}
