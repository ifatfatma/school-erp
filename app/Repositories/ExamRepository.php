<?php

namespace App\Repositories;

use App\Models\Exam;
use App\Services\TenantManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamRepository
{
    protected Exam $model;

    public function __construct(Exam $model)
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
     * Paginated List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $filterSchoolId = !empty($filters['school_id']) && $filters['school_id'] !== 'all' ? (int) $filters['school_id'] : null;
        $schoolId = $this->resolveSchoolId($filterSchoolId);

        $query = $this->model
            ->with([
                'academicSession',
                'examType',
                'creator',
                'updater',
                'school:id,name,code',
            ]);

        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        }

        return $query
            ->filter($filters)
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find Exam
     */
    public function find(int $id): Exam
    {
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);
        $isSuperAdmin = $tenantManager->isSuperAdmin($user);

        $query = $this->model
            ->with([
                'academicSession',
                'examType',
                'creator',
                'updater',
                'school:id,name,code',
            ]);

        if (!$isSuperAdmin && $user?->school_id) {
            $query->where('school_id', $user->school_id);
        }

        return $query->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): Exam
    {
        if (empty($data['school_id'])) {
            $data['school_id'] = $this->resolveSchoolId();
        }
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(Exam $exam, array $data): Exam
    {
        $exam->update($data);

        return $exam;
    }

    /**
     * Delete
     */
    public function delete(Exam $exam): bool
    {
        return $exam->delete();
    }
}
