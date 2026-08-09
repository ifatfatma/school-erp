<?php

namespace App\Services;

use App\Models\ExamType;
use App\Repositories\ExamTypeRepository;
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
    public function active()
    {
        return $this->repository->active();
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
        if ($this->repository->exists($data['name'])) {
            throw ValidationException::withMessages([
                'name' => ['Exam Type already exists.'],
            ]);
        }

        $data['school_id'] = Auth::user()->school_id;
        $data['created_by'] = Auth::id();

        return $this->repository->create($data);
    }

    /**
     * Update
     */
    public function update(ExamType $examType, array $data): ExamType
    {
        if ($this->repository->exists($data['name'], $examType->id)) {
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