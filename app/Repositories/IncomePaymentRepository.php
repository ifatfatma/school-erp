<?php

namespace App\Repositories;

use App\Models\Income;
use App\Models\IncomePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IncomePaymentRepository
{
    /**
     * List payments.
     */
    public function list(array $filters = [])
    {
        $schoolId = auth()->user()->school_id;

        $query = IncomePayment::query()
            ->where(
                'income_payments.school_id',
                $schoolId
            )
            ->with([
                'income.category',
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

            $query->where(function ($q) use ($search) {

                $q->where(
                    'income_payments.receipt_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'income_payments.transaction_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'income_payments.cheque_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'income',
                    function ($incomeQuery) use ($search) {

                        $incomeQuery
                            ->where(
                                'income_no',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'received_from',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Income
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['income_id'])) {

            $query->where(
                'income_payments.income_id',
                $filters['income_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Payment Method
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['payment_method'])) {

            $query->where(
                'income_payments.payment_method',
                $filters['payment_method']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['status'])) {

            $query->where(
                'income_payments.status',
                $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['date_from'])) {

            $query->whereDate(
                'income_payments.payment_date',
                '>=',
                $filters['date_from']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['date_to'])) {

            $query->whereDate(
                'income_payments.payment_date',
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
            'income_payments.payment_date'
        );

        $query->orderByDesc(
            'income_payments.id'
        );


        return $query->paginate(
            (int) (
                $filters['per_page']
                ?? 20
            )
        );
    }


    /**
     * Find payment.
     */
    public function find(
        int $id
    ): IncomePayment {

        return IncomePayment::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->with([
                'income.category',
            ])

            ->findOrFail($id);
    }


    /**
     * Create payment.
     */
    public function create(
        array $data
    ): IncomePayment {

        return DB::transaction(
            function () use ($data) {

                $schoolId =
                    auth()->user()->school_id;

                $userId =
                    auth()->id();


                /*
                |--------------------------------------------------------------------------
                | Lock income record
                |--------------------------------------------------------------------------
                */

                $income =
                    Income::query()

                        ->where(
                            'school_id',
                            $schoolId
                        )

                        ->lockForUpdate()

                        ->findOrFail(
                            $data['income_id']
                        );


                /*
                |--------------------------------------------------------------------------
                | Validate remaining amount
                |--------------------------------------------------------------------------
                */

                $paidAmount =
                    IncomePayment::query()

                        ->where(
                            'school_id',
                            $schoolId
                        )

                        ->where(
                            'income_id',
                            $income->id
                        )

                        ->where(
                            'status',
                            'Success'
                        )

                        ->sum('amount');


                $remaining =
                    (float) $income->amount
                    - (float) $paidAmount;


                $paymentAmount =
                    (float) $data['amount'];


                if (
                    $paymentAmount <= 0
                ) {

                    throw ValidationException::withMessages([
                        'amount' =>
                            'Payment amount must be greater than zero.',
                    ]);
                }


                if (
                    $paymentAmount > $remaining
                ) {

                    throw ValidationException::withMessages([
                        'amount' =>
                            'Payment amount cannot exceed the remaining income amount of ₹'
                            . number_format(
                                $remaining,
                                2
                            ),
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | School
                |--------------------------------------------------------------------------
                */

                $data['school_id'] =
                    $schoolId;


                /*
                |--------------------------------------------------------------------------
                | Receipt
                |--------------------------------------------------------------------------
                */

                if (
                    empty(
                        $data['receipt_no']
                        ?? null
                    )
                ) {

                    $data['receipt_no'] =
                        $this->generateReceiptNo(
                            $schoolId
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | User
                |--------------------------------------------------------------------------
                */

                $data['created_by'] =
                    $userId;

                $data['updated_by'] =
                    $userId;


                /*
                |--------------------------------------------------------------------------
                | Default Status
                |--------------------------------------------------------------------------
                */

                $data['status'] =
                    $data['status']
                    ?? 'Success';


                return IncomePayment::create(
                    $data
                );
            }
        );
    }


    /**
     * Update payment.
     */
    public function update(
        int $id,
        array $data
    ): IncomePayment {

        return DB::transaction(
            function () use (
                $id,
                $data
            ) {

                $payment =
                    IncomePayment::query()

                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )

                        ->lockForUpdate()

                        ->findOrFail($id);


                /*
                |--------------------------------------------------------------------------
                | If amount is being changed,
                | validate against income
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'amount',
                        $data
                    )
                ) {

                    $newAmount =
                        (float) $data['amount'];


                    if (
                        $newAmount <= 0
                    ) {

                        throw ValidationException::withMessages([
                            'amount' =>
                                'Payment amount must be greater than zero.',
                        ]);
                    }


                    $income =
                        Income::query()

                            ->where(
                                'school_id',
                                auth()->user()->school_id
                            )

                            ->lockForUpdate()

                            ->findOrFail(
                                $payment->income_id
                            );


                    $otherPaid =
                        IncomePayment::query()

                            ->where(
                                'school_id',
                                auth()->user()->school_id
                            )

                            ->where(
                                'income_id',
                                $income->id
                            )

                            ->where(
                                'id',
                                '!=',
                                $payment->id
                            )

                            ->where(
                                'status',
                                'Success'
                            )

                            ->sum('amount');


                    $remaining =
                        (float) $income->amount
                        - (float) $otherPaid;


                    if (
                        $newAmount >
                        $remaining
                    ) {

                        throw ValidationException::withMessages([
                            'amount' =>
                                'Payment amount cannot exceed the remaining income amount of ₹'
                                . number_format(
                                    $remaining,
                                    2
                                ),
                        ]);
                    }
                }


                $data['updated_by'] =
                    auth()->id();


                $payment->update(
                    $data
                );


                return $payment->fresh([
                    'income.category',
                ]);
            }
        );
    }


    /**
     * Delete payment.
     */
    public function delete(
        int $id
    ): bool {

        $payment =
            $this->find($id);

        return (bool) $payment->delete();
    }


    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        string $status
    ): IncomePayment {

        $payment =
            $this->find($id);


        $payment->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);


        return $payment->fresh([
            'income.category',
        ]);
    }


    /**
     * Generate receipt number.
     */
    public function generateReceiptNo(
        int $schoolId
    ): string {

        $year =
            now()->format('Y');


        $lastPayment =
            IncomePayment::query()

                ->where(
                    'school_id',
                    $schoolId
                )

                ->whereYear(
                    'created_at',
                    $year
                )

                ->latest('id')

                ->lockForUpdate()

                ->first();


        $number = 1;


        if ($lastPayment) {

            if (
                preg_match(
                    '/(\d+)$/',
                    $lastPayment->receipt_no,
                    $matches
                )
            ) {

                $number =
                    ((int) $matches[1]) + 1;
            }
        }


        return sprintf(
            'INR-%s-%06d',
            $year,
            $number
        );
    }


    /**
     * Get total successful payments.
     */
    public function total(
        array $filters = []
    ): float {

        $query =
            IncomePayment::query()

                ->where(
                    'income_payments.school_id',
                    auth()->user()->school_id
                );


        $this->applyFilters(
            $query,
            $filters
        );


        return (float) $query

            ->where(
                'income_payments.status',
                'Success'
            )

            ->sum(
                'income_payments.amount'
            );
    }


    /**
     * Payment summary.
     */
    public function summary(
        array $filters = []
    ): array {

        $query =
            IncomePayment::query()

                ->where(
                    'income_payments.school_id',
                    auth()->user()->school_id
                );


        $this->applyFilters(
            $query,
            $filters
        );


        return [

            'total_amount' =>
                (float) (clone $query)
                    ->sum(
                        'income_payments.amount'
                    ),

            'success_amount' =>
                (float) (clone $query)
                    ->where(
                        'income_payments.status',
                        'Success'
                    )
                    ->sum(
                        'income_payments.amount'
                    ),

            'pending_amount' =>
                (float) (clone $query)
                    ->where(
                        'income_payments.status',
                        'Pending'
                    )
                    ->sum(
                        'income_payments.amount'
                    ),

            'cancelled_amount' =>
                (float) (clone $query)
                    ->where(
                        'income_payments.status',
                        'Cancelled'
                    )
                    ->sum(
                        'income_payments.amount'
                    ),

        ];
    }


    /**
     * Remaining amount for an income.
     */
    public function remainingAmount(
        int $incomeId
    ): float {

        $schoolId =
            auth()->user()->school_id;


        $income =
            Income::query()

                ->where(
                    'school_id',
                    $schoolId
                )

                ->findOrFail(
                    $incomeId
                );


        $paid =
            IncomePayment::query()

                ->where(
                    'school_id',
                    $schoolId
                )

                ->where(
                    'income_id',
                    $incomeId
                )

                ->where(
                    'status',
                    'Success'
                )

                ->sum(
                    'amount'
                );


        return max(
            0,
            (float) $income->amount -
            (float) $paid
        );
    }


    /**
     * Apply filters.
     */
    protected function applyFilters(
        $query,
        array $filters
    ): void {

        if (
            !empty(
                $filters['income_id']
                ?? null
            )
        ) {

            $query->where(
                'income_payments.income_id',
                $filters['income_id']
            );
        }


        if (
            !empty(
                $filters['payment_method']
                ?? null
            )
        ) {

            $query->where(
                'income_payments.payment_method',
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
                'income_payments.status',
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
                'income_payments.payment_date',
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
                'income_payments.payment_date',
                '<=',
                $filters['date_to']
            );
        }
    }
}