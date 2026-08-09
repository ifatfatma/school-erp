<?php

namespace App\Services;

use App\Repositories\ReportCardRepository;

class ReportCardService
{
    public function __construct(
        protected ReportCardRepository $repository
    ) {
    }

    public function generate(
        int $studentId,
        int $examId
    ): array {
        return $this->repository->generate(
            $studentId,
            $examId
        );
    }
}