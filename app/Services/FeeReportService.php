<?php

namespace App\Services;

use App\Repositories\FeeReportRepository;

class FeeReportService
{
    public function __construct(
        protected FeeReportRepository $repository
    ) {
    }

    /**
     * Collection report.
     */
    public function collection(array $filters = [])
    {
        return $this->repository->collection(
            $filters
        );
    }

    /**
     * Collection summary.
     */
    public function collectionSummary(
        array $filters = []
    ): array {
        return $this->repository->collectionSummary(
            $filters
        );
    }

    /**
     * Outstanding dues.
     */
    public function dues(array $filters = [])
    {
        return $this->repository->dues(
            $filters
        );
    }
}