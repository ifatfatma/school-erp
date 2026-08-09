<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamMark\BulkStoreExamMarkRequest;
use App\Services\ExamMarkService;
use Illuminate\Http\Request;

class ExamMarkController extends Controller
{
    protected ExamMarkService $service;

    public function __construct(
        ExamMarkService $service
    ) {
        $this->service = $service;
    }

    /**
     * Load Marks Entry Screen
     */
    /*  public function load(Request $request)
    {
        return response()->json(

            $this->service->load(
                $request->exam_schedule_id
            )

        );
    } */

   public function load(Request $request)
{
    $request->validate([
        'exam_id'    => ['required', 'integer'],
        'class_id'   => ['required', 'integer'],
        'section_id' => ['required', 'integer'],
        'subject_id' => ['required', 'integer'],
    ]);

    $data = $this->service->load(
        (int) $request->exam_id,
        (int) $request->class_id,
        (int) $request->section_id,
        (int) $request->subject_id
    );

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

    /**
     * Bulk Save
     */
    public function save(
        BulkStoreExamMarkRequest $request
    ) {

        $this->service->save(

            $request->exam_id,
            $request->class_id,
            $request->section_id,
            $request->subject_id,

            $request->marks

        );

        return response()->json([

            'success' => true,

            'message' => 'Marks saved successfully.'

        ]);
    }

    /**
     * Student Result
     */
    public function student($student)
    {
        return response()->json([

            'success' => true,

            'data' => $this->service->studentMarks(
                $student
            )

        ]);
    }
}
