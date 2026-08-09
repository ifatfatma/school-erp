<?php

namespace App\Repositories;

use App\Models\ExamSchedule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamScheduleRepository
{
    protected ExamSchedule $model;

    public function __construct(ExamSchedule $model)
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
                'section',
                'subject',
                'teacher',
                'creator',
                'updater',
            ])

            ->where('school_id', auth()->user()->school_id)

            ->filter($filters)

            ->orderBy('exam_date')

            ->orderBy('start_time')

            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find
     */
    public function find(int $id): ExamSchedule
    {
        return $this->model

            ->with([
                'exam',
                'class',
                'section',
                'subject',
                'teacher',
                'creator',
                'updater',
            ])

            ->where('school_id', auth()->user()->school_id)

            ->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): ExamSchedule
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(
        ExamSchedule $schedule,
        array $data
    ): ExamSchedule {

        $schedule->update($data);

        return $schedule->fresh([
            'exam',
            'class',
            'section',
            'subject',
            'teacher',
            'creator',
            'updater',
        ]);
    }

    /**
     * Delete
     */
    public function delete(ExamSchedule $schedule): bool
    {
        return $schedule->delete();
    }

    /**
     * Active
     */
    public function active()
    {
        return $this->model

            ->where('school_id', auth()->user()->school_id)

            ->where('status', true)

            ->orderBy('exam_date')

            ->orderBy('start_time')

            ->get();
    }

    /**
     * Duplicate Subject Schedule
     */
    public function exists(
        int $examId,
        int $classId,
        int $sectionId,
        int $subjectId,
        ?int $ignoreId = null
    ): bool {

        return $this->model

            ->where('school_id', auth()->user()->school_id)

            ->where('exam_id', $examId)

            ->where('class_id', $classId)

            ->where('section_id', $sectionId)

            ->where('subject_id', $subjectId)

            ->when(
                $ignoreId,
                fn($q) => $q->where('id', '!=', $ignoreId)
            )

            ->exists();
    }

    /**
     * Teacher Time Conflict
     */
    public function teacherConflict(
        int $teacherId,
        string $examDate,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null
    ): bool {

        return $this->model

            ->where('teacher_id', $teacherId)

            ->whereDate('exam_date', $examDate)

            ->when(
                $ignoreId,
                fn($q) => $q->where('id', '!=', $ignoreId)
            )

            ->where(function ($q) use ($startTime, $endTime) {

                $q

                    ->whereBetween('start_time', [$startTime, $endTime])

                    ->orWhereBetween('end_time', [$startTime, $endTime])

                    ->orWhere(function ($qq) use ($startTime, $endTime) {

                        $qq

                            ->where('start_time', '<=', $startTime)

                            ->where('end_time', '>=', $endTime);

                    });

            })

            ->exists();
    }

    /**
     * Class Time Conflict
     */
    public function classConflict(
        int $classId,
        int $sectionId,
        string $examDate,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null
    ): bool {

        return $this->model

            ->where('class_id', $classId)

            ->where('section_id', $sectionId)

            ->whereDate('exam_date', $examDate)

            ->when(
                $ignoreId,
                fn($q) => $q->where('id', '!=', $ignoreId)
            )

            ->where(function ($q) use ($startTime, $endTime) {

                $q

                    ->whereBetween('start_time', [$startTime, $endTime])

                    ->orWhereBetween('end_time', [$startTime, $endTime])

                    ->orWhere(function ($qq) use ($startTime, $endTime) {

                        $qq

                            ->where('start_time', '<=', $startTime)

                            ->where('end_time', '>=', $endTime);

                    });

            })

            ->exists();
    }
}