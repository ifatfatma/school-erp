<?php

namespace App\Services;

use App\Repositories\ExamRankRepository;

class ExamRankService
{
    public function __construct(
        protected ExamRankRepository $repository
    ) {
    }

    public function calculate(
        int $examId,
        int $classId,
        int $sectionId
    ): array {

        $schoolId =
            auth()->user()->school_id;

        return $this->repository->calculate(
            $examId,
            $classId,
            $sectionId,
            $schoolId
        );
    }
}