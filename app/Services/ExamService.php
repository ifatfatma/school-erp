<?php

namespace App\Services;

use App\Models\Exam;
use App\Repositories\ExamRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExamService
{
    protected ExamRepository $repository;

    public function __construct(ExamRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Paginated List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Find
     */
    public function find(int $id): Exam
    {
        return $this->repository->find($id);
    }

    /**
     * Active Exams
     */
    public function active()
    {
        return $this->repository->active();
    }

    /**
     * Store
     */
    public function store(array $data): Exam
    {
        $this->validateDates($data);

        if (
            $this->repository->exists(
                $data['name'],
                $data['academic_session_id'],
                $data['exam_type_id']
            )
        ) {
            throw ValidationException::withMessages([
                'name' => ['Exam already exists for this Academic Session and Exam Type.']
            ]);
        }

        $data['school_id'] = Auth::user()->school_id;
        $data['created_by'] = Auth::id();

        return $this->repository->create($data);
    }

    /**
     * Update
     */
    public function update(Exam $exam, array $data): Exam
    {
        if ($exam->is_marks_locked) {
            throw ValidationException::withMessages([
                'exam' => ['Marks are locked. This exam cannot be modified.']
            ]);
        }

        $this->validateDates($data);

        if (
            $this->repository->exists(
                $data['name'],
                $data['academic_session_id'],
                $data['exam_type_id'],
                $exam->id
            )
        ) {
            throw ValidationException::withMessages([
                'name' => ['Exam already exists for this Academic Session and Exam Type.']
            ]);
        }

        $data['updated_by'] = Auth::id();

        return $this->repository->update($exam, $data);
    }

    /**
     * Delete
     */
    public function delete(Exam $exam): bool
    {
        if ($exam->is_marks_locked) {
            throw ValidationException::withMessages([
                'exam' => ['Locked exams cannot be deleted.']
            ]);
        }

        return $this->repository->delete($exam);
    }

    /**
     * Lock Marks
     */
    public function lockMarks(Exam $exam): Exam
    {
        return $this->repository->update($exam, [
            'is_marks_locked' => true,
            'updated_by'      => Auth::id(),
        ]);
    }

    /**
     * Unlock Marks
     */
    public function unlockMarks(Exam $exam): Exam
    {
        return $this->repository->update($exam, [
            'is_marks_locked' => false,
            'updated_by'      => Auth::id(),
        ]);
    }

    /**
     * Publish Result
     */
    public function publishResult(Exam $exam): Exam
    {
        return $this->repository->update($exam, [
            'is_result_published' => true,
            'updated_by'          => Auth::id(),
        ]);
    }

    /**
     * Unpublish Result
     */
    public function unpublishResult(Exam $exam): Exam
    {
        return $this->repository->update($exam, [
            'is_result_published' => false,
            'updated_by'          => Auth::id(),
        ]);
    }

    /**
     * Validate Dates
     */
    protected function validateDates(array $data): void
    {
        if (
            isset($data['start_date'], $data['end_date']) &&
            strtotime($data['end_date']) < strtotime($data['start_date'])
        ) {
            throw ValidationException::withMessages([
                'end_date' => [
                    'End Date must be greater than or equal to Start Date.'
                ]
            ]);
        }
    }
}