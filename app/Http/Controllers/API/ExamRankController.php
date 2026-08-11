<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExamRankService;
use Illuminate\Http\Request;

class ExamRankController extends Controller
{
    public function __construct(
        protected ExamRankService $service
    ) {
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => [
                'required',
                'integer',
                'exists:exams,id',
            ],

            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
            ],
        ]);

        $data = $this->service->calculate(
            $validated['exam_id'],
            $validated['class_id'],
            $validated['section_id']
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}