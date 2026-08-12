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

        return $this->repository->calculate(
            $examId,
            $classId,
            $sectionId,
            auth()->user()->school_id
        );
    }
}