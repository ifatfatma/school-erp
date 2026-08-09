<?php

namespace App\Services;

use App\Repositories\ExamMarkRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\ExamSchedule;
use App\Models\ExamSubject;
class ExamMarkService
{
    protected ExamMarkRepository $repository;

    public function __construct(
        ExamMarkRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Load Marks Entry Screen
     */
    /* public function load(int $scheduleId): array
    {
        return $this->repository->load($scheduleId);
    } */

public function load(
    int $examId,
    int $classId,
    int $sectionId,
    int $subjectId
): array {

    $schedule = ExamSchedule::where([
        'exam_id'    => $examId,
        'class_id'   => $classId,
        'section_id' => $sectionId,
        'subject_id' => $subjectId,
    ])->firstOrFail();

    $examSubject = ExamSubject::where([
        'exam_id'    => $examId,
        'class_id'   => $classId,
        'subject_id' => $subjectId,
    ])->firstOrFail();

    $schedule->setRelation('examSubject', $examSubject);

    return $this->repository->load($schedule->id);
}
    /**
     * Student Result
     */
    public function studentMarks(int $studentId)
    {
        return $this->repository->studentMarks($studentId);
    }

    /**
     * Bulk Save
     */
    public function save(
        int $scheduleId,
        array $rows
    ): void {

        $schedule = $this->repository->schedule($scheduleId);

        /*
        |--------------------------------------------------------------------------
        | Exam Locked
        |--------------------------------------------------------------------------
        */

        if ($schedule->exam->is_marks_locked) {

            throw ValidationException::withMessages([
                'exam' => [
                    'Marks have been locked for this examination.'
                ]
            ]);

        }

        $config = $schedule->examSubject;

        if (!$config) {

            throw ValidationException::withMessages([
                'exam_subject' => [
                    'Exam Subject configuration not found.'
                ]
            ]);

        }

        foreach ($rows as &$row) {

            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */

            if (
                ($row['attendance'] ?? 'Present')
                === 'Absent'
            ) {

                $row['theory_marks'] = 0;
                $row['practical_marks'] = 0;
                $row['viva_marks'] = 0;
                $row['project_marks'] = 0;
                $row['total_marks'] = 0;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Default Values
            |--------------------------------------------------------------------------
            */

            $row['theory_marks'] =
                (float)($row['theory_marks'] ?? 0);

            $row['practical_marks'] =
                (float)($row['practical_marks'] ?? 0);

            $row['viva_marks'] =
                (float)($row['viva_marks'] ?? 0);

            $row['project_marks'] =
                (float)($row['project_marks'] ?? 0);

            /*
            |--------------------------------------------------------------------------
            | Component Validation
            |--------------------------------------------------------------------------
            */

            if ($row['theory_marks'] > $config->theory_marks) {

                throw ValidationException::withMessages([
                    'theory_marks' => [
                        "Theory Marks cannot exceed {$config->theory_marks}."
                    ]
                ]);

            }

            if ($row['practical_marks'] > $config->practical_marks) {

                throw ValidationException::withMessages([
                    'practical_marks' => [
                        "Practical Marks cannot exceed {$config->practical_marks}."
                    ]
                ]);

            }

            if ($row['viva_marks'] > $config->viva_marks) {

                throw ValidationException::withMessages([
                    'viva_marks' => [
                        "Viva Marks cannot exceed {$config->viva_marks}."
                    ]
                ]);

            }

            if ($row['project_marks'] > $config->project_marks) {

                throw ValidationException::withMessages([
                    'project_marks' => [
                        "Project Marks cannot exceed {$config->project_marks}."
                    ]
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | Total
            |--------------------------------------------------------------------------
            */

            $row['total_marks'] =

                $row['theory_marks']

                +

                $row['practical_marks']

                +

                $row['viva_marks']

                +

                $row['project_marks'];

            if (
                $row['total_marks'] >
                $config->full_marks
            ) {

                throw ValidationException::withMessages([
                    'total_marks' => [
                        'Total Marks exceed Full Marks.'
                    ]
                ]);

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $this->repository->save(

            $scheduleId,

            $rows,

            Auth::user()->school_id,

            Auth::id()

        );
    }
}