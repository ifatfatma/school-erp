<?php

namespace App\Repositories;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamRank;
use App\Models\ExamSchedule;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ExamRankRepository
{
    /**
     * Calculate ranks for one exam/class/section.
     */
    public function calculate(
        int $examId,
        int $classId,
        int $sectionId,
        int $schoolId
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Exam
        |--------------------------------------------------------------------------
        */

        $exam = Exam::query()
            ->where('school_id', $schoolId)
            ->findOrFail($examId);


        /*
        |--------------------------------------------------------------------------
        | Validate Section belongs to Class
        |--------------------------------------------------------------------------
        */

        $sectionExists = DB::table('sections')
            ->where('id', $sectionId)
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('status', true)
            ->exists();

        if (!$sectionExists) {
            abort(422, 'Selected section does not belong to selected class.');
        }


        /*
        |--------------------------------------------------------------------------
        | Exam schedules
        |--------------------------------------------------------------------------
        */

        $schedules = ExamSchedule::query()
            ->where('school_id', $schoolId)
            ->where('exam_id', $examId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->with([
                'examSubject',
                'subject',
            ])
            ->orderBy('id')
            ->get();


        if ($schedules->isEmpty()) {
            return [];
        }


        $scheduleIds = $schedules->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $students = Student::query()
            ->where('school_id', $schoolId)
            ->where(
                'academic_session_id',
                $exam->academic_session_id
            )
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('status', true)
            ->orderBy('roll_no')
            ->get();


        if ($students->isEmpty()) {
            return [];
        }


        /*
        |--------------------------------------------------------------------------
        | Marks
        |--------------------------------------------------------------------------
        */

        $marks = ExamMark::query()
            ->where('school_id', $schoolId)
            ->whereIn('exam_schedule_id', $scheduleIds)
            ->whereIn(
                'student_id',
                $students->pluck('id')
            )
            ->get()
            ->groupBy('student_id');


        /*
        |--------------------------------------------------------------------------
        | Calculate student totals
        |--------------------------------------------------------------------------
        */

        $results = [];


        foreach ($students as $student) {

            $studentMarks =
                $marks->get($student->id, collect());


            $totalFullMarks = 0;

            $totalObtainedMarks = 0;


            foreach ($schedules as $schedule) {

                $fullMarks = (float) (
                    $schedule->examSubject->full_marks
                    ?? $schedule->full_marks
                    ?? 0
                );

                $totalFullMarks += $fullMarks;


                $mark = $studentMarks->firstWhere(
                    'exam_schedule_id',
                    $schedule->id
                );


                if ($mark) {
                    $totalObtainedMarks +=
                        (float) $mark->total_marks;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Percentage
            |--------------------------------------------------------------------------
            */

            $percentage = $totalFullMarks > 0
                ? round(
                    ($totalObtainedMarks / $totalFullMarks) * 100,
                    2
                )
                : 0;


            /*
            |--------------------------------------------------------------------------
            | Determine whether marks are entered
            |--------------------------------------------------------------------------
            */

            $enteredSubjects =
                $studentMarks->count();


            $results[] = [

                'student_id' =>
                    $student->id,

                'student' =>
                    $student,

                'total_subjects' =>
                    $schedules->count(),

                'entered_subjects' =>
                    $enteredSubjects,

                'total_full_marks' =>
                    $totalFullMarks,

                'total_obtained_marks' =>
                    $totalObtainedMarks,

                'percentage' =>
                    $percentage,

                'rank' =>
                    null,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Rank only students having marks
        |--------------------------------------------------------------------------
        */

        $rankable = collect($results)
            ->filter(function ($item) {

                return $item['entered_subjects'] > 0;
            })
            ->sortByDesc('percentage')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Competition Rank
        |
        | 95 = 1
        | 90 = 2
        | 90 = 2
        | 85 = 4
        |--------------------------------------------------------------------------
        */

        $lastPercentage = null;
        $currentRank = 0;


        foreach ($rankable as $index => $item) {

            if (
                $lastPercentage === null ||
                (float) $item['percentage'] !==
                (float) $lastPercentage
            ) {

                $currentRank = $index + 1;
            }


            $resultsIndex = collect($results)
                ->search(
                    fn ($result) =>
                        $result['student_id'] ===
                        $item['student_id']
                );


            if ($resultsIndex !== false) {

                $results[$resultsIndex]['rank'] =
                    $currentRank;
            }


            $lastPercentage =
                $item['percentage'];
        }


        /*
        |--------------------------------------------------------------------------
        | Sort final result
        |--------------------------------------------------------------------------
        */

        usort(
            $results,
            function ($a, $b) {

                /*
                 * Ranked students first.
                 */
                if (
                    $a['rank'] !== null &&
                    $b['rank'] === null
                ) {
                    return -1;
                }

                if (
                    $a['rank'] === null &&
                    $b['rank'] !== null
                ) {
                    return 1;
                }


                /*
                 * Percentage descending.
                 */
                if (
                    $a['percentage'] !=
                    $b['percentage']
                ) {
                    return
                        $b['percentage']
                        <=>
                        $a['percentage'];
                }


                /*
                 * Roll number.
                 */
                return
                    ((int) ($a['student']->roll_no ?? 0))
                    <=>
                    ((int) ($b['student']->roll_no ?? 0));
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Save ranks
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $results,
            $exam,
            $examId,
            $classId,
            $sectionId,
            $schoolId
        ) {

            foreach ($results as $result) {

                /*
                 * Don't save rank for students
                 * without any marks.
                 */
                if ($result['rank'] === null) {

                    ExamRank::query()
                        ->where('exam_id', $examId)
                        ->where('class_id', $classId)
                        ->where('section_id', $sectionId)
                        ->where(
                            'student_id',
                            $result['student_id']
                        )
                        ->delete();

                    continue;
                }


                ExamRank::updateOrCreate(

                    [
                        'exam_id' =>
                            $examId,

                        'class_id' =>
                            $classId,

                        'section_id' =>
                            $sectionId,

                        'student_id' =>
                            $result['student_id'],
                    ],

                    [
                        'school_id' =>
                            $schoolId,

                        'academic_session_id' =>
                            $exam->academic_session_id,

                        'total_full_marks' =>
                            $result['total_full_marks'],

                        'total_obtained_marks' =>
                            $result['total_obtained_marks'],

                        'percentage' =>
                            $result['percentage'],

                        'rank' =>
                            $result['rank'],
                    ]
                );
            }
        });


        return $results;
    }
}