<?php

namespace App\Repositories;

use App\Models\Income;
use Illuminate\Support\Facades\DB;

class IncomeReportRepository
{
    protected function baseQuery(array $filters = [])
    {
        $schoolId = auth()->user()->school_id;

        $query = Income::query()
            ->where(
                'incomes.school_id',
                $schoolId
            );

        if (!empty($filters['date_from'])) {
            $query->whereDate(
                'incomes.income_date',
                '>=',
                $filters['date_from']
            );
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate(
                'incomes.income_date',
                '<=',
                $filters['date_to']
            );
        }

        if (!empty($filters['category_id'])) {
            $query->where(
                'incomes.income_category_id',
                $filters['category_id']
            );
        }

        if (!empty($filters['status'])) {
            $query->where(
                'incomes.status',
                $filters['status']
            );
        }

        return $query;
    }


    /**
     * Overall summary.
     */
    public function summary(
        array $filters = []
    ): array {

        $query =
            $this->baseQuery($filters);

        $result =
            $query->selectRaw('
                COUNT(incomes.id) as total_records,

                COALESCE(
                    SUM(incomes.amount),
                    0
                ) as total_amount,

                COALESCE(
                    SUM(
                        CASE
                            WHEN incomes.status = "Received"
                            THEN incomes.amount
                            ELSE 0
                        END
                    ),
                    0
                ) as received_amount,

                COALESCE(
                    SUM(
                        CASE
                            WHEN incomes.status = "Pending"
                            THEN incomes.amount
                            ELSE 0
                        END
                    ),
                    0
                ) as pending_amount,

                COALESCE(
                    SUM(
                        CASE
                            WHEN incomes.status = "Cancelled"
                            THEN incomes.amount
                            ELSE 0
                        END
                    ),
                    0
                ) as cancelled_amount
            ')
            ->first();

        return [
            'total_records' =>
                (int) $result->total_records,

            'total_amount' =>
                (float) $result->total_amount,

            'received_amount' =>
                (float) $result->received_amount,

            'pending_amount' =>
                (float) $result->pending_amount,

            'cancelled_amount' =>
                (float) $result->cancelled_amount,
        ];
    }


    /**
     * Daily report.
     */
    public function daily(
        array $filters = []
    ) {

        return $this->baseQuery($filters)
            ->select([
                DB::raw(
                    'DATE(incomes.income_date) as report_date'
                ),

                DB::raw(
                    'COUNT(incomes.id) as total_records'
                ),

                DB::raw(
                    'SUM(incomes.amount) as total_amount'
                ),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Received'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as received_amount
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Pending'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as pending_amount
                "),
            ])
            ->groupBy(
                DB::raw(
                    'DATE(incomes.income_date)'
                )
            )
            ->orderBy(
                'report_date',
                'desc'
            )
            ->get();
    }


    /**
     * Monthly report.
     */
    public function monthly(
        array $filters = []
    ) {

        return $this->baseQuery($filters)
            ->select([
                DB::raw(
                    'YEAR(incomes.income_date) as report_year'
                ),

                DB::raw(
                    'MONTH(incomes.income_date) as report_month'
                ),

                DB::raw("
                    DATE_FORMAT(
                        incomes.income_date,
                        '%Y-%m'
                    ) as month
                "),

                DB::raw(
                    'COUNT(incomes.id) as total_records'
                ),

                DB::raw(
                    'SUM(incomes.amount) as total_amount'
                ),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Received'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as received_amount
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Pending'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as pending_amount
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Cancelled'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as cancelled_amount
                "),
            ])
            ->groupBy(
                DB::raw(
                    'YEAR(incomes.income_date)'
                ),
                DB::raw(
                    'MONTH(incomes.income_date)'
                ),
                DB::raw(
                    "DATE_FORMAT(
                        incomes.income_date,
                        '%Y-%m'
                    )"
                )
            )
            ->orderBy(
                'report_year',
                'desc'
            )
            ->orderBy(
                'report_month',
                'desc'
            )
            ->get();
    }


    /**
     * Category-wise report.
     */
    public function categoryWise(
        array $filters = []
    ) {

        return $this->baseQuery($filters)
            ->leftJoin(
                'income_categories',
                'incomes.income_category_id',
                '=',
                'income_categories.id'
            )
            ->select([
                'incomes.income_category_id',

                'income_categories.name',

                DB::raw(
                    'COUNT(incomes.id) as total_records'
                ),

                DB::raw(
                    'SUM(incomes.amount) as total_amount'
                ),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Received'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as received_amount
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Pending'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as pending_amount
                "),
            ])
            ->groupBy(
                'incomes.income_category_id',
                'income_categories.name'
            )
            ->orderByDesc(
                'total_amount'
            )
            ->get();
    }


    /**
     * Payment-method-wise report.
     */
    public function paymentMethodWise(
        array $filters = []
    ) {

        return $this->baseQuery($filters)
            ->select([
                'incomes.payment_method',

                DB::raw(
                    'COUNT(incomes.id) as total_records'
                ),

                DB::raw(
                    'SUM(incomes.amount) as total_amount'
                ),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Received'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as received_amount
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Pending'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as pending_amount
                "),
            ])
            ->groupBy(
                'incomes.payment_method'
            )
            ->orderByDesc(
                'total_amount'
            )
            ->get();
    }


    /**
     * Yearly report.
     */
    public function yearly(
        array $filters = []
    ) {

        return $this->baseQuery($filters)
            ->select([
                DB::raw(
                    'YEAR(incomes.income_date) as year'
                ),

                DB::raw(
                    'COUNT(incomes.id) as total_records'
                ),

                DB::raw(
                    'SUM(incomes.amount) as total_amount'
                ),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Received'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as received_amount
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Pending'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as pending_amount
                "),

                DB::raw("
                    SUM(
                        CASE
                            WHEN incomes.status = 'Cancelled'
                            THEN incomes.amount
                            ELSE 0
                        END
                    ) as cancelled_amount
                "),
            ])
            ->groupBy(
                DB::raw(
                    'YEAR(incomes.income_date)'
                )
            )
            ->orderByDesc(
                'year'
            )
            ->get();
    }
}