<?php

namespace App\Repositories;

use App\Models\ExamMark;
use App\Models\ExamRank;
use App\Models\ExamSchedule;
use Illuminate\Support\Facades\DB;

class ExamRankRepository
{
    public function calculate(
        int $examId,
        int $classId,
        int $sectionId,
        int $schoolId
    ): array {

        $schedules = ExamSchedule::query()
            ->where('school_id', $schoolId)
            ->where('exam_id', $examId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->with(['examSubject','exam'])
            ->get();

        $scheduleIds = $schedules->pluck('id');

        if ($scheduleIds->isEmpty()) {
            return [];
        }

        $marks = ExamMark::query()
            ->where('school_id', $schoolId)
            ->whereIn('exam_schedule_id', $scheduleIds)
            ->with('student')
            ->get();

        $students = [];

        foreach ($marks->groupBy('student_id') as $studentId => $studentMarks) {

            $totalFullMarks = 0;
            $totalObtained = 0;

            foreach ($studentMarks as $mark) {

                $schedule = $schedules->firstWhere(
                    'id',
                    $mark->exam_schedule_id
                );

                if (!$schedule) {
                    continue;
                }

                $fullMarks = (float) (
                    $schedule->examSubject->full_marks
                    ?? $schedule->full_marks
                    ?? 0
                );

                $totalFullMarks += $fullMarks;
                $totalObtained += (float) $mark->total_marks;
            }

            $percentage = $totalFullMarks > 0
                ? round(
                    ($totalObtained / $totalFullMarks) * 100,
                    2
                )
                : 0;

            $student = $studentMarks->first()->student;

            $students[] = [
                'student_id' => $studentId,
                'student' => $student,
                'total_full_marks' => $totalFullMarks,
                'total_obtained_marks' => $totalObtained,
                'percentage' => $percentage,
            ];
        }

        /*
         * Highest percentage first.
         */
        usort(
            $students,
            fn ($a, $b) =>
                $b['percentage'] <=> $a['percentage']
        );

        /*
         * Competition ranking:
         *
         * 95 = 1
         * 90 = 2
         * 90 = 2
         * 85 = 4
         */
        $lastPercentage = null;
        $rank = 0;

        foreach ($students as $index => &$student) {

            if (
                $lastPercentage === null ||
                $student['percentage'] != $lastPercentage
            ) {
                $rank = $index + 1;
            }

            $student['rank'] = $rank;

            $lastPercentage =
                $student['percentage'];
        }

        unset($student);

        /*
         * Save ranks.
         */
        DB::transaction(function () use (
            $students,
            $examId,
            $classId,
            $sectionId,
            $schoolId,
            $schedules
        ) {

            $exam = $schedules->first()->exam;

            foreach ($students as $student) {

                ExamRank::updateOrCreate(
                    [
                        'exam_id' => $examId,
                        'class_id' => $classId,
                        'section_id' => $sectionId,
                        'student_id' => $student['student_id'],
                    ],
                    [
                        'school_id' => $schoolId,
                        'academic_session_id' =>
                            $exam->academic_session_id,

                        'total_full_marks' =>
                            $student['total_full_marks'],

                        'total_obtained_marks' =>
                            $student['total_obtained_marks'],

                        'percentage' =>
                            $student['percentage'],

                        'rank' =>
                            $student['rank'],
                    ]
                );
            }
        });

        return $students;
    }
}