<?php

namespace App\Services;

use App\Repositories\ExpenseReportRepository;

class ExpenseReportService
{
    public function __construct(
        protected ExpenseReportRepository $repository
    ) {
    }

    /**
     * Complete expense report.
     */
    public function report(array $filters = [])
    {
        return $this->repository->report(
            $filters
        );
    }

    /**
     * Category-wise report.
     */
    public function categorySummary(
        array $filters = []
    ) {
        return $this->repository->categorySummary(
            $filters
        );
    }

    /**
     * Payment-method-wise report.
     */
    public function paymentMethodSummary(
        array $filters = []
    ) {
        return $this->repository->paymentMethodSummary(
            $filters
        );
    }

    /**
     * Daily report.
     */
    public function dailySummary(
        array $filters = []
    ) {
        return $this->repository->dailySummary(
            $filters
        );
    }
}