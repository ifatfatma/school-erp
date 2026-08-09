<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportCardService;

class ReportCardController extends Controller
{
    public function __construct(
        protected ReportCardService $service
    ) {
    }

    public function student(
        int $student,
        int $exam
    ) {
        return response()->json([
            'success' => true,

            'data' => $this->service->generate(
                $student,
                $exam
            ),
        ]);
    }
}