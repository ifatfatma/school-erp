<?php

namespace App\Repositories;

use App\Models\FeePayment;

class PaymentHistoryRepository
{
    /**
     * Get payment history.
     */
    public function list(array $filters = [])
    {
        return FeePayment::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->with([
                'student',
                'items',
                'items.feeDue',
                'items.feeDue.feeType',
                'items.feeDue.feeStructure',
            ])

            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'receipt_no',
                            'like',
                            '%' . $search . '%'
                        )

                        ->orWhere(
                            'transaction_no',
                            'like',
                            '%' . $search . '%'
                        )

                        ->orWhereHas(
                            'student',
                            function ($studentQuery) use ($search) {

                                $studentQuery
                                    ->where(
                                        'name',
                                        'like',
                                        '%' . $search . '%'
                                    )

                                    ->orWhere(
                                        'admission_no',
                                        'like',
                                        '%' . $search . '%'
                                    );

                            }
                        );

                    });

                }
            )


            /*
            |--------------------------------------------------------------------------
            | Payment Method
            |--------------------------------------------------------------------------
            */

            ->when(
                $filters['payment_method'] ?? null,
                function ($query, $value) {

                    $query->where(
                        'payment_method',
                        $value
                    );

                }
            )


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            ->when(
                $filters['status'] ?? null,
                function ($query, $value) {

                    $query->where(
                        'status',
                        $value
                    );

                }
            )


            /*
            |--------------------------------------------------------------------------
            | Date From
            |--------------------------------------------------------------------------
            */

            ->when(
                $filters['date_from'] ?? null,
                function ($query, $value) {

                    $query->whereDate(
                        'payment_date',
                        '>=',
                        $value
                    );

                }
            )


            /*
            |--------------------------------------------------------------------------
            | Date To
            |--------------------------------------------------------------------------
            */

            ->when(
                $filters['date_to'] ?? null,
                function ($query, $value) {

                    $query->whereDate(
                        'payment_date',
                        '<=',
                        $value
                    );

                }
            )


            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            ->when(
                $filters['student_id'] ?? null,
                function ($query, $value) {

                    $query->where(
                        'student_id',
                        $value
                    );

                }
            )


            /*
            |--------------------------------------------------------------------------
            | Academic Session
            |--------------------------------------------------------------------------
            */

            ->when(
                $filters['academic_session_id'] ?? null,
                function ($query, $value) {

                    $query->where(
                        'academic_session_id',
                        $value
                    );

                }
            )


            /*
            |--------------------------------------------------------------------------
            | Latest First
            |--------------------------------------------------------------------------
            */

            ->latest('id')


            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */

            ->paginate(
                (int) (
                    $filters['per_page']
                    ?? 20
                )
            );
    }


    /**
     * Get payment details.
     */
    public function find(
        int $id
    ): FeePayment {

        return FeePayment::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->with([
                'student',
                'items',
                'items.feeDue',
                'items.feeDue.feeType',
                'items.feeDue.feeStructure',
            ])

            ->findOrFail($id);
    }


    /**
     * Get payments for a specific student.
     */
    public function studentPayments(
        int $studentId
    ) {

        return FeePayment::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->where(
                'student_id',
                $studentId
            )

            ->with([
                'student',
                'items',
                'items.feeDue',
                'items.feeDue.feeType',
                'items.feeDue.feeStructure',
            ])

            ->latest('id')

            ->get();
    }


    /**
     * Get payments for a specific fee due.
     */
    public function duePayments(
        int $dueId
    ) {

        return FeePayment::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->whereHas(
                'items',
                function ($query) use ($dueId) {

                    $query->where(
                        'student_fee_due_id',
                        $dueId
                    );

                }
            )

            ->with([
                'student',
                'items',
                'items.feeDue',
                'items.feeDue.feeType',
                'items.feeDue.feeStructure',
            ])

            ->latest('id')

            ->get();
    }

    /**
 * Get payment receipt data.
 */
public function receipt(int $paymentId): FeePayment
{
    return FeePayment::query()
        ->where(
            'school_id',
            auth()->user()->school_id
        )
        ->with([
            'student',
            'items',
            'items.feeDue',
            'items.feeDue.feeType',
            'items.feeDue.feeStructure',
        ])
        ->findOrFail($paymentId);
}
}