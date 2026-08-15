<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Models\ExpensePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExpensePaymentRepository
{
    /**
     * List payments.
     */
    public function list(
        array $filters = []
    ) {
        return ExpensePayment::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->with([
                'expense',
                'expense.category',
                'creator',
                'updater',
            ])

            ->filter($filters)

            ->latest(
                'payment_date'
            )

            ->latest('id')

            ->paginate(
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
    ): ExpensePayment {

        return ExpensePayment::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->with([
                'expense',
                'expense.category',
                'creator',
                'updater',
            ])

            ->findOrFail($id);
    }


    /**
     * Create payment.
     */
    public function create(
        array $data
    ): ExpensePayment {

        return DB::transaction(
            function () use ($data) {

                $schoolId =
                    auth()->user()->school_id;


                $expense =
                    Expense::query()

                        ->where(
                            'school_id',
                            $schoolId
                        )

                        ->lockForUpdate()

                        ->findOrFail(
                            $data['expense_id']
                        );


                /*
                 * Calculate already successful
                 * payments.
                 */
                $paidAmount =
                    (float) ExpensePayment::query()

                        ->where(
                            'school_id',
                            $schoolId
                        )

                        ->where(
                            'expense_id',
                            $expense->id
                        )

                        ->where(
                            'status',
                            'Success'
                        )

                        ->sum('amount');


                $expenseAmount =
                    (float) $expense->amount;


                $newAmount =
                    (float) $data['amount'];


                $remaining =
                    $expenseAmount -
                    $paidAmount;


                if ($newAmount > $remaining) {

                    throw ValidationException::withMessages([

                        'amount' => [
                            'Payment amount cannot be greater than the remaining expense amount of ' .
                            number_format(
                                max(
                                    $remaining,
                                    0
                                ),
                                2
                            ) . '.'
                        ],

                    ]);
                }


                $data['school_id'] =
                    $schoolId;

                $data['created_by'] =
                    auth()->id();

                $data['updated_by'] =
                    auth()->id();


                $payment =
                    ExpensePayment::create(
                        $data
                    );


                /*
                 * Update expense status.
                 */
                $totalPaid =
                    $paidAmount +
                    $newAmount;


                if ($totalPaid >= $expenseAmount) {

                    $expenseStatus =
                        'Paid';

                } elseif ($totalPaid > 0) {

                    $expenseStatus =
                        'Pending';

                } else {

                    $expenseStatus =
                        'Pending';

                }


                $expense->update([

                    'status' =>
                        $expenseStatus,

                    'updated_by' =>
                        auth()->id(),

                ]);


                return $payment->load([
                    'expense',
                    'expense.category',
                    'creator',
                    'updater',
                ]);
            }
        );
    }


    /**
     * Update payment.
     */
    public function update(
        int $id,
        array $data
    ): ExpensePayment {

        return DB::transaction(
            function () use (
                $id,
                $data
            ) {

                $payment =
                    $this->find($id);


                $expense =
                    Expense::query()

                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )

                        ->lockForUpdate()

                        ->findOrFail(
                            $payment->expense_id
                        );


                /*
                 * Existing successful amount
                 * excluding this payment.
                 */
                $otherPaid =
                    (float) ExpensePayment::query()

                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )

                        ->where(
                            'expense_id',
                            $expense->id
                        )

                        ->where(
                            'status',
                            'Success'
                        )

                        ->where(
                            'id',
                            '!=',
                            $payment->id
                        )

                        ->sum('amount');


                $newAmount =
                    (float) $data['amount'];


                if (
                    $data['status'] ===
                    'Success'
                ) {

                    $remaining =
                        (float) $expense->amount -
                        $otherPaid;


                    if (
                        $newAmount >
                        $remaining
                    ) {

                        throw ValidationException::withMessages([

                            'amount' => [
                                'Payment amount cannot be greater than the remaining expense amount of ' .
                                number_format(
                                    max(
                                        $remaining,
                                        0
                                    ),
                                    2
                                ) . '.'
                            ],

                        ]);
                    }
                }


                $data['updated_by'] =
                    auth()->id();


                $payment->update(
                    $data
                );


                /*
                 * Recalculate total paid.
                 */
                $totalPaid =
                    (float) ExpensePayment::query()

                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )

                        ->where(
                            'expense_id',
                            $expense->id
                        )

                        ->where(
                            'status',
                            'Success'
                        )

                        ->sum('amount');


                if (
                    $totalPaid >=
                    (float) $expense->amount
                ) {

                    $expenseStatus =
                        'Paid';

                } elseif (
                    $totalPaid > 0
                ) {

                    $expenseStatus =
                        'Pending';

                } else {

                    $expenseStatus =
                        'Pending';

                }


                $expense->update([

                    'status' =>
                        $expenseStatus,

                    'updated_by' =>
                        auth()->id(),

                ]);


                return $payment->fresh([
                    'expense',
                    'expense.category',
                    'creator',
                    'updater',
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

        return DB::transaction(
            function () use ($id) {

                $payment =
                    $this->find($id);


                $expense =
                    Expense::query()

                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )

                        ->lockForUpdate()

                        ->findOrFail(
                            $payment->expense_id
                        );


                $deleted =
                    $payment->delete();


                /*
                 * Recalculate expense status.
                 */
                $paidAmount =
                    (float) ExpensePayment::query()

                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )

                        ->where(
                            'expense_id',
                            $expense->id
                        )

                        ->where(
                            'status',
                            'Success'
                        )

                        ->sum('amount');


                if (
                    $paidAmount >=
                    (float) $expense->amount
                ) {

                    $status = 'Paid';

                } elseif (
                    $paidAmount > 0
                ) {

                    $status = 'Pending';

                } else {

                    $status = 'Pending';

                }


                $expense->update([

                    'status' =>
                        $status,

                    'updated_by' =>
                        auth()->id(),

                ]);


                return $deleted;
            }
        );
    }


    /**
     * Payment summary for an expense.
     */
    public function summary(
        int $expenseId
    ): array {

        $expense =
            Expense::query()

                ->where(
                    'school_id',
                    auth()->user()->school_id
                )

                ->findOrFail(
                    $expenseId
                );


        $paid =
            (float) ExpensePayment::query()

                ->where(
                    'school_id',
                    auth()->user()->school_id
                )

                ->where(
                    'expense_id',
                    $expenseId
                )

                ->where(
                    'status',
                    'Success'
                )

                ->sum('amount');


        $pending =
            (float) ExpensePayment::query()

                ->where(
                    'school_id',
                    auth()->user()->school_id
                )

                ->where(
                    'expense_id',
                    $expenseId
                )

                ->where(
                    'status',
                    'Pending'
                )

                ->sum('amount');


        $cancelled =
            (float) ExpensePayment::query()

                ->where(
                    'school_id',
                    auth()->user()->school_id
                )

                ->where(
                    'expense_id',
                    $expenseId
                )

                ->where(
                    'status',
                    'Cancelled'
                )

                ->sum('amount');


        return [

            'expense_amount' =>
                (float) $expense->amount,

            'paid_amount' =>
                $paid,

            'pending_amount' =>
                $pending,

            'cancelled_amount' =>
                $cancelled,

            'due_amount' =>
                max(
                    (float) $expense->amount -
                    $paid,
                    0
                ),

        ];
    }
}