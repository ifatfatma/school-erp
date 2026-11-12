<?php

namespace App\Repositories;

use App\Models\ExamType;
use App\Services\TenantManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamTypeRepository
{
    protected ExamType $model;

    public function __construct(ExamType $model)
    {
        $this->model = $model;
    }

    protected function resolveSchoolId(?int $filterSchoolId = null): ?int
    {
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);

        if (!$tenantManager->isSuperAdmin($user)) {
            return $user?->school_id ? (int) $user->school_id : null;
        }

        if ($filterSchoolId !== null && $filterSchoolId > 0) {
            return (int) $filterSchoolId;
        }

        $tenantId = $tenantManager->getTenantId();
        if ($tenantId) {
            return (int) $tenantId;
        }

        return $user?->school_id ? (int) $user->school_id : null;
    }

    /**
     * List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $schoolId = $this->resolveSchoolId(!empty($filters['school_id']) ? (int) $filters['school_id'] : null);

        return $this->model
            ->with('school:id,name,code')
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->filter($filters)
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find
     */
    public function find(int $id): ExamType
    {
        $schoolId = $this->resolveSchoolId();

        return $this->model
            ->with('school:id,name,code')
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): ExamType
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(ExamType $examType, array $data): ExamType
    {
        $examType->update($data);

        return $examType->fresh();
    }

    /**
     * Delete
     */
    public function delete(ExamType $examType): bool
    {
        return $examType->delete();
    }

    /**
     * Active List
     */
    public function active(?int $schoolId = null)
    {
        $targetSchoolId = $schoolId ?? $this->resolveSchoolId();

        return $this->model
            ->with('school:id,name,code')
            ->when($targetSchoolId, fn($q) => $q->where('school_id', $targetSchoolId))
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Exists
     */
    public function exists(string $name, ?int $ignoreId = null, ?int $schoolId = null): bool
    {
        $targetSchoolId = $schoolId ?? $this->resolveSchoolId();

        return $this->model
            ->when($targetSchoolId, fn($q) => $q->where('school_id', $targetSchoolId))
            ->where('name', $name)
            ->when(
                $ignoreId,
                fn($q) => $q->where('id', '!=', $ignoreId)
            )
            ->exists();
    }
}
