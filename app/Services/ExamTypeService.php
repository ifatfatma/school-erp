<?php

namespace App\Services;

use App\Models\ExamType;
use App\Repositories\ExamTypeRepository;
use App\Services\TenantManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExamTypeService
{
    protected ExamTypeRepository $repository;

    public function __construct(ExamTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated list
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Get active exam types
     */
    public function active(?int $schoolId = null)
    {
        return $this->repository->active($schoolId);
    }

    /**
     * Find exam type
     */
    public function find(int $id): ExamType
    {
        return $this->repository->find($id);
    }

    /**
     * Store
     */
    public function store(array $data): ExamType
    {
        $tenantId = app(TenantManager::class)->getTenantId();
        $schoolId = !empty($data['school_id']) ? (int) $data['school_id'] : ($tenantId ?: (Auth::user()?->school_id ?: 1));
        $data['school_id'] = $schoolId;

        if ($this->repository->exists($data['name'], null, $schoolId)) {
            throw ValidationException::withMessages([
                'name' => ['Exam Type already exists.'],
            ]);
        }

        $data['created_by'] = Auth::id();

        return $this->repository->create($data);
    }

    /**
     * Update
     */
    public function update(ExamType $examType, array $data): ExamType
    {
        $schoolId = !empty($data['school_id']) ? (int) $data['school_id'] : $examType->school_id;

        if ($this->repository->exists($data['name'], $examType->id, $schoolId)) {
            throw ValidationException::withMessages([
                'name' => ['Exam Type already exists.'],
            ]);
        }

        $data['updated_by'] = Auth::id();

        return $this->repository->update($examType, $data);
    }

    /**
     * Delete
     */
    public function delete(ExamType $examType): bool
    {
        return $this->repository->delete($examType);
    }
}
