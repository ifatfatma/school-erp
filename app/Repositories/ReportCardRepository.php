<?php

namespace App\Repositories;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamSchedule;
use App\Models\Student;
use App\Models\ResultGrade as Grade;
use App\Services\TenantManager;

class ReportCardRepository
{
    public function generate(
        int $studentId,
        int $examId
    ): array {
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);
        $isSuperAdmin = $user && $tenantManager->isSuperAdmin($user);
        $activeSchoolId = $tenantManager->getTenantId() ?? $user?->school_id;

        $studentQuery = Student::query()->with([
            'class',
            'section',
        ]);
        if (!$isSuperAdmin || $activeSchoolId) {
            $studentQuery->where('school_id', $activeSchoolId ?? $user?->school_id);
        }
        $student = $studentQuery->findOrFail($studentId);

        $schoolId = $student->school_id ?? $activeSchoolId ?? $user?->school_id;

        $examQuery = Exam::query()->with([
            'academicSession',
            'examType',
        ]);
        if (!$isSuperAdmin || $schoolId) {
            $examQuery->where('school_id', $schoolId);
        }
        $exam = $examQuery->findOrFail($examId);

        /*
        |--------------------------------------------------------------------------
        | Make sure student belongs to exam session
        |--------------------------------------------------------------------------
        */

        abort_if(
            (int) $student->academic_session_id !==
            (int) $exam->academic_session_id,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Get exam schedules for student's class/section
        |--------------------------------------------------------------------------
        */

        $schedules = ExamSchedule::query()
            ->where('school_id', auth()->user()->school_id)
            ->where('exam_id', $exam->id)
            ->where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->with([
                'subject',
                'examSubject',
            ])
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Get student's marks
        |--------------------------------------------------------------------------
        */

        $marks = ExamMark::query()
            ->where('school_id', auth()->user()->school_id)
            ->where('student_id', $student->id)
            ->whereIn(
                'exam_schedule_id',
                $schedules->pluck('id')
            )
            ->get()
            ->keyBy('exam_schedule_id');

        $subjects = [];

        $totalFullMarks = 0;
        $totalObtained = 0;

        $failed = false;

        foreach ($schedules as $schedule) {

            $mark = $marks->get($schedule->id);

            $fullMarks = (float) (
                $schedule->examSubject->full_marks
                ?? $schedule->full_marks
                ?? 0
            );

            $passMarks = (float) (
                $schedule->examSubject->pass_marks
                ?? $schedule->pass_marks
                ?? 0
            );

            $obtained = $mark
                ? (float) $mark->total_marks
                : 0;

            $percentage = $fullMarks > 0
                ? round(
                    ($obtained / $fullMarks) * 100,
                    2
                )
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Grade
            |--------------------------------------------------------------------------
            */

            $grade = Grade::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where('status', true)
                ->where(
                    'min_percentage',
                    '<=',
                    $percentage
                )
                ->where(
                    'max_percentage',
                    '>=',
                    $percentage
                )
                ->orderByDesc('min_percentage')
                ->first();

            $isPass = $mark
                ? $obtained >= $passMarks
                : false;

            if (!$isPass) {
                $failed = true;
            }

            if (
                $mark &&
                $mark->attendance === 'Absent'
            ) {
                $isPass = false;
                $failed = true;
            }

            $totalFullMarks += $fullMarks;
            $totalObtained += $obtained;

            $subjects[] = [
                'schedule_id' => $schedule->id,

                'subject' => $schedule->subject,

                'full_marks' => $fullMarks,

                'pass_marks' => $passMarks,

                'theory_marks' => $mark
                    ? (float) $mark->theory_marks
                    : 0,

                'practical_marks' => $mark
                    ? (float) $mark->practical_marks
                    : 0,

                'viva_marks' => $mark
                    ? (float) $mark->viva_marks
                    : 0,

                'project_marks' => $mark
                    ? (float) $mark->project_marks
                    : 0,

                'total_marks' => $obtained,

                'percentage' => $percentage,

                'grade' => $grade,

                'attendance' => $mark
                    ? $mark->attendance
                    : null,

                'result' => $mark
                    ? (
                        $mark->attendance === 'Absent'
                            ? 'Absent'
                            : ($isPass ? 'Pass' : 'Fail')
                    )
                    : 'Not Entered',

                'remarks' => $mark?->remarks,
            ];
        }

        $overallPercentage = $totalFullMarks > 0
            ? round(
                ($totalObtained / $totalFullMarks) * 100,
                2
            )
            : 0;

        $overallGrade = Grade::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->where('status', true)
            ->where(
                'min_percentage',
                '<=',
                $overallPercentage
            )
            ->where(
                'max_percentage',
                '>=',
                $overallPercentage
            )
            ->orderByDesc('min_percentage')
            ->first();

        return [
            'student' => $student,

            'exam' => $exam,

            'subjects' => $subjects,

            'summary' => [
                'total_subjects' => count($subjects),

                'total_full_marks' => $totalFullMarks,

                'total_obtained_marks' => $totalObtained,

                'percentage' => $overallPercentage,

                'grade' => $overallGrade,

                'result' => $failed
                    ? 'Fail'
                    : (
                        count($subjects)
                            ? 'Pass'
                            : 'Not Published'
                    ),
            ],
        ];
    }
}