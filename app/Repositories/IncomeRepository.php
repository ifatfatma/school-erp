<?php

namespace App\Repositories;

use App\Models\Income;
use Illuminate\Support\Facades\DB;

class IncomeRepository
{
    /**
     * List incomes.
     */
    public function list(array $filters = [])
    {
        $schoolId = auth()->user()->school_id;

        $query = Income::query()
            ->where(
                'incomes.school_id',
                $schoolId
            )
            ->with([
                'category',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {

            $search = trim(
                $filters['search']
            );

            $query->where(function ($q) use (
                $search
            ) {

                $q->where(
                    'incomes.income_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'incomes.received_from',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'incomes.reference_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'incomes.description',
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
                $filters['income_category_id']
                ?? null
            )
        ) {

            $query->where(
                'incomes.income_category_id',
                $filters[
                    'income_category_id'
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
                'incomes.payment_method',
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
                'incomes.status',
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
                'incomes.income_date',
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
                'incomes.income_date',
                '<=',
                $filters['date_to']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderByDesc(
            'incomes.income_date'
        );

        $query->orderByDesc(
            'incomes.id'
        );


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        return $query->paginate(
            (int) (
                $filters['per_page']
                ?? 20
            )
        );
    }


    /**
     * Find income.
     */
    public function find(
        int $id
    ): Income {

        return Income::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->with([
                'category',
            ])

            ->findOrFail($id);
    }


    /**
     * Create income.
     */
    public function create(
        array $data
    ): Income {

        $schoolId =
            auth()->user()->school_id;

        $userId =
            auth()->id();


        $data['school_id'] =
            $schoolId;

        $data['created_by'] =
            $userId;

        $data['updated_by'] =
            $userId;


        /*
        |--------------------------------------------------------------------------
        | Generate Income Number
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['income_no']
                ?? null
            )
        ) {

            $data['income_no'] =
                $this->generateIncomeNo(
                    $schoolId
                );
        }


        return Income::create(
            $data
        );
    }


    /**
     * Update income.
     */
    public function update(
        int $id,
        array $data
    ): Income {

        $income =
            $this->find($id);


        $data['updated_by'] =
            auth()->id();


        $income->update(
            $data
        );


        return $income->fresh([
            'category',
        ]);
    }


    /**
     * Delete income.
     */
    public function delete(
        int $id
    ): bool {

        $income =
            $this->find($id);


        return (bool)
            $income->delete();
    }


    /**
     * Change income status.
     */
    public function changeStatus(
        int $id,
        string $status
    ): Income {

        $income =
            $this->find($id);


        $income->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);


        return $income->fresh([
            'category',
        ]);
    }


    /**
     * Generate unique income number.
     */
    public function generateIncomeNo(
        int $schoolId
    ): string {

        $year =
            now()->format('Y');


        return DB::transaction(
            function () use (
                $schoolId,
                $year
            ) {

                $lastIncome =
                    Income::query()

                        ->where(
                            'school_id',
                            $schoolId
                        )

                        ->whereYear(
                            'created_at',
                            $year
                        )

                        ->lockForUpdate()

                        ->latest('id')

                        ->first();


                $number = 1;


                if ($lastIncome) {

                    $lastNo =
                        $lastIncome->income_no;


                    if (
                        preg_match(
                            '/(\d+)$/',
                            $lastNo,
                            $matches
                        )
                    ) {

                        $number =
                            ((int)
                                $matches[1]
                            ) + 1;
                    }
                }


                return sprintf(
                    'INC-%s-%06d',
                    $year,
                    $number
                );
            }
        );
    }


    /**
     * Check duplicate income number.
     */
    public function existsByIncomeNo(
        string $incomeNo,
        ?int $ignoreId = null
    ): bool {

        $query = Income::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->where(
                'income_no',
                $incomeNo
            );


        if ($ignoreId) {

            $query->where(
                'id',
                '!=',
                $ignoreId
            );
        }


        return $query->exists();
    }


    /**
     * Get total income.
     */
    public function total(
        array $filters = []
    ): float {

        $query = Income::query()
            ->where(
                'incomes.school_id',
                auth()->user()->school_id
            );


        $this->applyFilters(
            $query,
            $filters
        );


        return (float) $query->sum(
            'incomes.amount'
        );
    }


    /**
     * Get received income.
     */
    public function receivedTotal(
        array $filters = []
    ): float {

        $query = Income::query()
            ->where(
                'incomes.school_id',
                auth()->user()->school_id
            );


        $this->applyFilters(
            $query,
            $filters
        );


        return (float) $query

            ->where(
                'incomes.status',
                'Received'
            )

            ->sum(
                'incomes.amount'
            );
    }


    /**
     * Get pending income.
     */
    public function pendingTotal(
        array $filters = []
    ): float {

        $query = Income::query()
            ->where(
                'incomes.school_id',
                auth()->user()->school_id
            );


        $this->applyFilters(
            $query,
            $filters
        );


        return (float) $query

            ->where(
                'incomes.status',
                'Pending'
            )

            ->sum(
                'incomes.amount'
            );
    }


    /**
     * Get cancelled income.
     */
    public function cancelledTotal(
        array $filters = []
    ): float {

        $query = Income::query()
            ->where(
                'incomes.school_id',
                auth()->user()->school_id
            );


        $this->applyFilters(
            $query,
            $filters
        );


        return (float) $query

            ->where(
                'incomes.status',
                'Cancelled'
            )

            ->sum(
                'incomes.amount'
            );
    }


    /**
     * Active income categories.
     */
    public function categories()
    {
        return \App\Models\IncomeCategory::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->where(
                'status',
                true
            )

            ->orderBy(
                'name'
            )

            ->get();
    }


    /**
     * Apply common filters.
     */
    protected function applyFilters(
        $query,
        array $filters
    ): void {

        if (
            !empty(
                $filters['search']
                ?? null
            )
        ) {

            $search =
                trim(
                    $filters['search']
                );


            $query->where(function ($q) use (
                $search
            ) {

                $q->where(
                    'incomes.income_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'incomes.received_from',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'incomes.reference_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'incomes.description',
                    'like',
                    "%{$search}%"
                );

            });
        }


        if (
            !empty(
                $filters['income_category_id']
                ?? null
            )
        ) {

            $query->where(
                'incomes.income_category_id',
                $filters[
                    'income_category_id'
                ]
            );
        }


        if (
            !empty(
                $filters['payment_method']
                ?? null
            )
        ) {

            $query->where(
                'incomes.payment_method',
                $filters['payment_method']
            );
        }


        if (
            !empty(
                $filters['status']
                ?? null
            )
        ) {

            $query->where(
                'incomes.status',
                $filters['status']
            );
        }


        if (
            !empty(
                $filters['date_from']
                ?? null
            )
        ) {

            $query->whereDate(
                'incomes.income_date',
                '>=',
                $filters['date_from']
            );
        }


        if (
            !empty(
                $filters['date_to']
                ?? null
            )
        ) {

            $query->whereDate(
                'incomes.income_date',
                '<=',
                $filters['date_to']
            );
        }
    }
}