<?php

namespace App\Repositories;

use App\Models\ExamMark;
use App\Models\ExamSchedule;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ExamMarkRepository
{
    protected ExamMark $model;

    public function __construct(ExamMark $model)
    {
        $this->model = $model;
    }

    /**
     * Load Schedule
     */
    public function schedule(int $scheduleId): ExamSchedule
    {
        $schedule = ExamSchedule::with([
            'exam',
            'class',
            'section',
            'subject',
        ])->findOrFail($scheduleId);

        $schedule->load([
            'examSubject' => function ($query) use ($schedule) {

                $query->where('exam_id', $schedule->exam_id)
                    ->where('class_id', $schedule->class_id);
            }
        ]);

        return $schedule;
    }
    /**
     * Load Students
     */
    /* public function students(int $scheduleId)
    {
        $schedule = $this->schedule($scheduleId);

        return Student::query()

            ->where('school_id', auth()->user()->school_id)

            ->where('academic_session_id', $schedule->exam->academic_session_id)

            ->where('class_id', $schedule->class_id)

            ->where('section_id', $schedule->section_id)

            ->orderBy('roll_no')

            ->get();
    } */
    public function students(int $scheduleId)
{
    $schedule = $this->schedule($scheduleId);

    \Log::info('Marks Entry Student Filter', [
        'school_id' => auth()->user()->school_id,
        'academic_session_id' => $schedule->exam->academic_session_id,
        'class_id' => $schedule->class_id,
        'section_id' => $schedule->section_id,
    ]);

    return Student::query()

        ->where('school_id', auth()->user()->school_id)

        ->where(
            'academic_session_id',
            $schedule->exam->academic_session_id
        )

        ->where('class_id', $schedule->class_id)

        ->where('section_id', $schedule->section_id)

        ->orderBy('roll_no')

        ->get();
}
    /**
     * Existing Marks
     */
    public function existingMarks(int $scheduleId)
    {
        return $this->model

            ->where('exam_schedule_id', $scheduleId)

            ->get()

            ->keyBy('student_id');
    }

    /**
     * Load Screen
     */
    public function load(int $scheduleId): array
    {
        return [

            'schedule' => $this->schedule($scheduleId),

            'students' => $this->students($scheduleId),

            'marks' => $this->existingMarks($scheduleId)

        ];
    }

    /**
     * Bulk Save
     */
    public function save(
        int $scheduleId,
        array $rows,
        int $schoolId,
        int $userId
    ): void {

        DB::transaction(function () use (
            $scheduleId,
            $rows,
            $schoolId,
            $userId
        ) {

            foreach ($rows as $row) {

                ExamMark::updateOrCreate(

                    [

                        'exam_schedule_id' => $scheduleId,

                        'student_id' => $row['student_id']

                    ],

                    [

                        'school_id' => $schoolId,

                        'theory_marks' =>
                        $row['theory_marks'] ?? 0,

                        'practical_marks' =>
                        $row['practical_marks'] ?? 0,

                        'viva_marks' =>
                        $row['viva_marks'] ?? 0,

                        'project_marks' =>
                        $row['project_marks'] ?? 0,

                        'total_marks' =>
                        $row['total_marks'] ?? 0,

                        'attendance' =>
                        $row['attendance'],

                        'remarks' =>
                        $row['remarks'] ?? null,

                        'updated_by' => $userId,

                        'created_by' => $userId,

                    ]

                );
            }
        });
    }

    /**
     * Student Result
     */
    public function studentMarks(int $studentId)
    {
        return $this->model

            ->with([
                'schedule.exam',
                'schedule.subject'
            ])

            ->where('student_id', $studentId)

            ->orderBy('exam_schedule_id')

            ->get();
    }
}
