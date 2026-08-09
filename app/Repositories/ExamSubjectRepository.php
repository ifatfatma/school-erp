<?php

namespace App\Repositories;

use App\Models\ExamSubject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamSubjectRepository
{
    protected ExamSubject $model;

    public function __construct(ExamSubject $model)
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
                'exam',
                'class',
                'subject',
                'creator',
                'updater'
            ])
            ->where('school_id', auth()->user()->school_id)
            ->filter($filters)
            ->orderBy('display_order')
            ->orderBy('subject_id')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find
     */
    public function find(int $id): ExamSubject
    {
        return $this->model
            ->with([
                'exam',
                'class',
                'subject',
                'creator',
                'updater'
            ])
            ->where('school_id', auth()->user()->school_id)
            ->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): ExamSubject
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(
        ExamSubject $examSubject,
        array $data
    ): ExamSubject {

        $examSubject->update($data);

        return $examSubject->fresh([
            'exam',
            'class',
            'subject',
            'creator',
            'updater'
        ]);
    }

    /**
     * Delete
     */
    public function delete(ExamSubject $examSubject): bool
    {
        return $examSubject->delete();
    }

    /**
     * Active List
     */
    public function active()
    {
        return $this->model
            ->with([
                'exam',
                'class',
                'subject'
            ])
            ->where('school_id', auth()->user()->school_id)
            ->where('status', true)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Check Duplicate
     */
    public function exists(
        int $examId,
        int $classId,
        int $subjectId,
        ?int $ignoreId = null
    ): bool {

        return $this->model
            ->where('school_id', auth()->user()->school_id)
            ->where('exam_id', $examId)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->when(
                $ignoreId,
                fn($q) => $q->where('id', '!=', $ignoreId)
            )
            ->exists();
    }

    /**
     * Subjects By Exam & Class
     */
    public function byExamAndClass(
        int $examId,
        int $classId
    )
    {
        return $this->model
            ->with('subject')
            ->where('school_id', auth()->user()->school_id)
            ->where('exam_id', $examId)
            ->where('class_id', $classId)
            ->where('status', true)
            ->orderBy('display_order')
            ->get();
    }
}