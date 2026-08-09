<?php

namespace App\Services;

use App\Models\ExamSchedule;
use App\Repositories\ExamScheduleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExamScheduleService
{
    protected ExamScheduleRepository $repository;

    public function __construct(ExamScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Find
     */
    public function find(int $id): ExamSchedule
    {
        return $this->repository->find($id);
    }

    /**
     * Active List
     */
    public function active()
    {
        return $this->repository->active();
    }

    /**
     * Store
     */
    public function store(array $data): ExamSchedule
    {
        $this->validateSchedule($data);

        $data['school_id'] = Auth::user()->school_id;
        $data['created_by'] = Auth::id();

        return $this->repository->create($data);
    }

    /**
     * Update
     */
    public function update(
        ExamSchedule $schedule,
        array $data
    ): ExamSchedule {

        $this->validateSchedule($data, $schedule->id);

        $data['updated_by'] = Auth::id();

        return $this->repository->update(
            $schedule,
            $data
        );
    }

    /**
     * Delete
     */
    public function delete(ExamSchedule $schedule): bool
    {
        return $this->repository->delete($schedule);
    }

    /**
     * Validate Schedule
     */
    protected function validateSchedule(
        array $data,
        ?int $ignoreId = null
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Duplicate Subject
        |--------------------------------------------------------------------------
        */

        if (
            $this->repository->exists(
                $data['exam_id'],
                $data['class_id'],
                $data['section_id'],
                $data['subject_id'],
                $ignoreId
            )
        ) {

            throw ValidationException::withMessages([
                'subject_id' => [
                    'This subject has already been scheduled for this exam.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Teacher Conflict
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['teacher_id']) &&
            $this->repository->teacherConflict(
                $data['teacher_id'],
                $data['exam_date'],
                $data['start_time'],
                $data['end_time'],
                $ignoreId
            )
        ) {

            throw ValidationException::withMessages([
                'teacher_id' => [
                    'Teacher already has another exam during this time.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Class Conflict
        |--------------------------------------------------------------------------
        */

        if (
            $this->repository->classConflict(
                $data['class_id'],
                $data['section_id'],
                $data['exam_date'],
                $data['start_time'],
                $data['end_time'],
                $ignoreId
            )
        ) {

            throw ValidationException::withMessages([
                'exam_date' => [
                    'Another exam is already scheduled for this class and section at the selected time.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Time Validation
        |--------------------------------------------------------------------------
        */

        if (
            strtotime($data['end_time']) <=
            strtotime($data['start_time'])
        ) {

            throw ValidationException::withMessages([
                'end_time' => [
                    'End Time must be greater than Start Time.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Marks Validation
        |--------------------------------------------------------------------------
        */

        if (
            $data['pass_marks'] >
            $data['full_marks']
        ) {

            throw ValidationException::withMessages([
                'pass_marks' => [
                    'Pass Marks cannot be greater than Full Marks.'
                ]
            ]);

        }
    }
}