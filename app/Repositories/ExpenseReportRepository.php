<?php

namespace App\Repositories;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseReportRepository
{
    /**
     * Complete expense report.
     */
    public function report(array $filters = [])
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = Expense::query()
            ->where(
                'expenses.school_id',
                $schoolId
            );

        $this->applyFilters(
            $query,
            $filters
        );


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalAmount = (clone $query)
            ->sum('expenses.amount');


        $paidAmount = (clone $query)
            ->where(
                'expenses.status',
                'Paid'
            )
            ->sum('expenses.amount');


        $pendingAmount = (clone $query)
            ->where(
                'expenses.status',
                'Pending'
            )
            ->sum('expenses.amount');


        $cancelledAmount = (clone $query)
            ->where(
                'expenses.status',
                'Cancelled'
            )
            ->sum('expenses.amount');


        /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */

        $totalRecords = (clone $query)
            ->count('expenses.id');


        $paidRecords = (clone $query)
            ->where(
                'expenses.status',
                'Paid'
            )
            ->count('expenses.id');


        $pendingRecords = (clone $query)
            ->where(
                'expenses.status',
                'Pending'
            )
            ->count('expenses.id');


        $cancelledRecords = (clone $query)
            ->where(
                'expenses.status',
                'Cancelled'
            )
            ->count('expenses.id');


        /*
        |--------------------------------------------------------------------------
        | Category Summary
        |--------------------------------------------------------------------------
        */

        $categoryQuery = Expense::query()
            ->where(
                'expenses.school_id',
                $schoolId
            );

        $this->applyFilters(
            $categoryQuery,
            $filters
        );


        $categorySummary = $categoryQuery

            ->leftJoin(
                'expense_categories',
                'expenses.expense_category_id',
                '=',
                'expense_categories.id'
            )

            ->select(
                'expenses.expense_category_id',
                'expense_categories.name',
                DB::raw(
                    'COUNT(expenses.id) as total_records'
                ),
                DB::raw(
                    'SUM(expenses.amount) as total_amount'
                )
            )

            ->groupBy(
                'expenses.expense_category_id',
                'expense_categories.name'
            )

            ->orderByDesc(
                'total_amount'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | Payment Method Summary
        |--------------------------------------------------------------------------
        */

        $paymentQuery = Expense::query()
            ->where(
                'expenses.school_id',
                $schoolId
            );

        $this->applyFilters(
            $paymentQuery,
            $filters
        );


        $paymentMethodSummary = $paymentQuery

            ->select(
                'expenses.payment_method',
                DB::raw(
                    'COUNT(expenses.id) as total_records'
                ),
                DB::raw(
                    'SUM(expenses.amount) as total_amount'
                )
            )

            ->groupBy(
                'expenses.payment_method'
            )

            ->orderByDesc(
                'total_amount'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | Daily Summary
        |--------------------------------------------------------------------------
        */

        $dailyQuery = Expense::query()
            ->where(
                'expenses.school_id',
                $schoolId
            );

        $this->applyFilters(
            $dailyQuery,
            $filters
        );


        $dailySummary = $dailyQuery

            ->select(
                'expenses.expense_date',
                DB::raw(
                    'COUNT(expenses.id) as total_records'
                ),
                DB::raw(
                    'SUM(expenses.amount) as total_amount'
                )
            )

            ->groupBy(
                'expenses.expense_date'
            )

            ->orderBy(
                'expenses.expense_date'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | Expense Details
        |--------------------------------------------------------------------------
        */

        $expenseQuery = Expense::query()
            ->where(
                'expenses.school_id',
                $schoolId
            );

        $this->applyFilters(
            $expenseQuery,
            $filters
        );


        $expenses = $expenseQuery

            ->with([
                'category'
            ])

            ->orderByDesc(
                'expenses.expense_date'
            )

            ->orderByDesc(
                'expenses.id'
            )

            ->paginate(
                (int) (
                    $filters['per_page']
                    ?? 20
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return [

            'summary' => [

                'total_amount' =>
                    (float) $totalAmount,

                'paid_amount' =>
                    (float) $paidAmount,

                'pending_amount' =>
                    (float) $pendingAmount,

                'cancelled_amount' =>
                    (float) $cancelledAmount,

                'total_records' =>
                    $totalRecords,

                'paid_records' =>
                    $paidRecords,

                'pending_records' =>
                    $pendingRecords,

                'cancelled_records' =>
                    $cancelledRecords,

            ],

            'category_summary' =>
                $categorySummary,

            'payment_method_summary' =>
                $paymentMethodSummary,

            'daily_summary' =>
                $dailySummary,

            'expenses' =>
                $expenses,

        ];
    }


    /**
     * Apply filters.
     */
    protected function applyFilters(
        $query,
        array $filters
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['search']
                ?? null
            )
        ) {

            $search =
                $filters['search'];


            $query->where(function ($q) use (
                $search
            ) {

                $q->where(
                    'expenses.expense_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'expenses.payee_name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'expenses.description',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'expenses.reference_no',
                    'like',
                    "%{$search}%"
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['expense_category_id']
                ?? null
            )
        ) {

            $query->where(
                'expenses.expense_category_id',
                $filters[
                    'expense_category_id'
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Payment Method
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['payment_method']
                ?? null
            )
        ) {

            $query->where(
                'expenses.payment_method',
                $filters['payment_method']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['status']
                ?? null
            )
        ) {

            $query->where(
                'expenses.status',
                $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | From Date
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['date_from']
                ?? null
            )
        ) {

            $query->whereDate(
                'expenses.expense_date',
                '>=',
                $filters['date_from']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | To Date
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['date_to']
                ?? null
            )
        ) {

            $query->whereDate(
                'expenses.expense_date',
                '<=',
                $filters['date_to']
            );
        }
    }


    /**
     * Category summary.
     */
    public function categorySummary(
        array $filters = []
    ) {

        $schoolId =
            auth()->user()->school_id;


        $query = Expense::query()
            ->where(
                'expenses.school_id',
                $schoolId
            );


        $this->applyFilters(
            $query,
            $filters
        );


        return $query

            ->leftJoin(
                'expense_categories',
                'expenses.expense_category_id',
                '=',
                'expense_categories.id'
            )

            ->select(
                'expenses.expense_category_id',
                'expense_categories.name',
                DB::raw(
                    'COUNT(expenses.id) as total_records'
                ),
                DB::raw(
                    'SUM(expenses.amount) as total_amount'
                )
            )

            ->groupBy(
                'expenses.expense_category_id',
                'expense_categories.name'
            )

            ->orderByDesc(
                'total_amount'
            )

            ->get();
    }


    /**
     * Payment method summary.
     */
    public function paymentMethodSummary(
        array $filters = []
    ) {

        $schoolId =
            auth()->user()->school_id;


        $query = Expense::query()
            ->where(
                'expenses.school_id',
                $schoolId
            );


        $this->applyFilters(
            $query,
            $filters
        );


        return $query

            ->select(
                'expenses.payment_method',
                DB::raw(
                    'COUNT(expenses.id) as total_records'
                ),
                DB::raw(
                    'SUM(expenses.amount) as total_amount'
                )
            )

            ->groupBy(
                'expenses.payment_method'
            )

            ->orderByDesc(
                'total_amount'
            )

            ->get();
    }


    /**
     * Daily summary.
     */
    public function dailySummary(
        array $filters = []
    ) {

        $schoolId =
            auth()->user()->school_id;


        $query = Expense::query()
            ->where(
                'expenses.school_id',
                $schoolId
            );


        $this->applyFilters(
            $query,
            $filters
        );


        return $query

            ->select(
                'expenses.expense_date',
                DB::raw(
                    'COUNT(expenses.id) as total_records'
                ),
                DB::raw(
                    'SUM(expenses.amount) as total_amount'
                )
            )

            ->groupBy(
                'expenses.expense_date'
            )

            ->orderBy(
                'expenses.expense_date'
            )

            ->get();
    }
}