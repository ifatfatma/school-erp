<?php

namespace App\Repositories;

use App\Models\Exam;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamRepository
{
    protected Exam $model;

    public function __construct(Exam $model)
    {
        $this->model = $model;
    }

    /**
     * Paginated List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->model

            ->with([
                'academicSession',
                'examType',
                'creator',
                'updater',
            ])

            ->where('school_id', auth()->user()->school_id)

            ->filter($filters)

            ->latest()

            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find Exam
     */
    public function find(int $id): Exam
    {
        return $this->model

            ->with([
                'academicSession',
                'examType',
                'creator',
                'updater',
            ])

            ->where('school_id', auth()->user()->school_id)

            ->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): Exam
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(Exam $exam, array $data): Exam
    {
        $exam->update($data);

        return $exam->fresh([
            'academicSession',
            'examType',
            'creator',
            'updater',
        ]);
    }

    /**
     * Delete
     */
    public function delete(Exam $exam): bool
    {
        return $exam->delete();
    }

    /**
     * Active Exams
     */
    public function active()
    {
        return $this->model

            ->where('school_id', auth()->user()->school_id)

            ->where('status', true)

            ->orderBy('start_date')

            ->get();
    }

    /**
     * Check Duplicate
     */
    public function exists(
        string $name,
        int $academicSessionId,
        int $examTypeId,
        ?int $ignoreId = null
    ): bool {

        return $this->model

            ->where('school_id', auth()->user()->school_id)

            ->where('academic_session_id', $academicSessionId)

            ->where('exam_type_id', $examTypeId)

            ->where('name', $name)

            ->when(
                $ignoreId,
                fn ($q) => $q->where('id', '!=', $ignoreId)
            )

            ->exists();
    }
}