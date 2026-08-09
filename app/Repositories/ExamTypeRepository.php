<?php

namespace App\Repositories;

use App\Models\ExamType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamTypeRepository
{
    protected ExamType $model;

    public function __construct(ExamType $model)
    {
        $this->model = $model;
    }

    /**
     * List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->where('school_id', auth()->user()->school_id)
            ->filter($filters)
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find
     */
    public function find(int $id): ExamType
    {
        return $this->model
            ->where('school_id', auth()->user()->school_id)
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
    public function active()
    {
        return $this->model
            ->where('school_id', auth()->user()->school_id)
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Exists
     */
    public function exists(string $name, ?int $ignoreId = null): bool
    {
        return $this->model
            ->where('school_id', auth()->user()->school_id)
            ->where('name', $name)
            ->when(
                $ignoreId,
                fn($q) => $q->where('id', '!=', $ignoreId)
            )
            ->exists();
    }
}