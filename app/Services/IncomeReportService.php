<?php

namespace App\Services;

use App\Repositories\IncomeReportRepository;

class IncomeReportService
{
    public function __construct(
        protected IncomeReportRepository $repository
    ) {
    }

    /**
     * Overall summary.
     */
    public function summary(array $filters = []): array
    {
        return $this->repository->summary($filters);
    }

    /**
     * Daily income report.
     */
    public function daily(array $filters = [])
    {
        return $this->repository->daily($filters);
    }

    /**
     * Monthly income report.
     */
    public function monthly(array $filters = [])
    {
        return $this->repository->monthly($filters);
    }

    /**
     * Yearly income report.
     */
    public function yearly(array $filters = [])
    {
        return $this->repository->yearly($filters);
    }

    /**
     * Category-wise income report.
     */
    public function categoryWise(array $filters = [])
    {
        return $this->repository->categoryWise($filters);
    }

    /**
     * Payment-method-wise income report.
     */
    public function paymentMethodWise(
        array $filters = []
    ) {
        return $this->repository->paymentMethodWise(
            $filters
        );
    }
}