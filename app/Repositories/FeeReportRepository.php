<?php

namespace App\Repositories;

use App\Models\FeePayment;
use App\Models\StudentFeeDue;
use Illuminate\Support\Facades\DB;

class FeeReportRepository
{
    /**
     * Collection report.
     */
    public function collection(array $filters = [])
    {
        $schoolId = auth()->user()->school_id;

        return FeePayment::query()

            ->where(
                'fee_payments.school_id',
                $schoolId
            )

            ->with([
                'student',
                'items',
                'items.feeDue',
                'items.feeDue.feeType',
                'items.feeDue.feeStructure',
            ])

            ->when(
                $filters['date_from'] ?? null,
                function ($query, $date) {

                    $query->whereDate(
                        'payment_date',
                        '>=',
                        $date
                    );

                }
            )

            ->when(
                $filters['date_to'] ?? null,
                function ($query, $date) {

                    $query->whereDate(
                        'payment_date',
                        '<=',
                        $date
                    );

                }
            )

            ->when(
                $filters['payment_method'] ?? null,
                function ($query, $method) {

                    $query->where(
                        'payment_method',
                        $method
                    );

                }
            )

            ->when(
                $filters['student_id'] ?? null,
                function ($query, $studentId) {

                    $query->where(
                        'student_id',
                        $studentId
                    );

                }
            )

            ->when(
                $filters['status'] ?? null,
                function ($query, $status) {

                    $query->where(
                        'status',
                        $status
                    );

                }
            )

            ->latest(
                'payment_date'
            )

            ->latest(
                'fee_payments.id'
            )

            ->get();
    }


    /**
     * Collection summary.
     */
    public function collectionSummary(
        array $filters = []
    ): array {

        $schoolId =
            auth()->user()->school_id;


        $query = FeePayment::query()

            ->where(
                'school_id',
                $schoolId
            );


        $this->applyPaymentFilters(
            $query,
            $filters
        );


        $totalPayments =
            (clone $query)->count();


        $totalCollection =
            (float) (
                (clone $query)
                    ->sum('total_amount')
            );


        $cash =
            (float) (
                (clone $query)
                    ->where(
                        'payment_method',
                        'Cash'
                    )
                    ->sum('total_amount')
            );


        $card =
            (float) (
                (clone $query)
                    ->where(
                        'payment_method',
                        'Card'
                    )
                    ->sum('total_amount')
            );


        $upi =
            (float) (
                (clone $query)
                    ->where(
                        'payment_method',
                        'UPI'
                    )
                    ->sum('total_amount')
            );


        $bankTransfer =
            (float) (
                (clone $query)
                    ->where(
                        'payment_method',
                        'Bank Transfer'
                    )
                    ->sum('total_amount')
            );


        $cheque =
            (float) (
                (clone $query)
                    ->where(
                        'payment_method',
                        'Cheque'
                    )
                    ->sum('total_amount')
            );


        $online =
            (float) (
                (clone $query)
                    ->where(
                        'payment_method',
                        'Online'
                    )
                    ->sum('total_amount')
            );


        return [

            'total_payments' =>
                $totalPayments,

            'total_collection' =>
                $totalCollection,

            'cash' =>
                $cash,

            'card' =>
                $card,

            'upi' =>
                $upi,

            'bank_transfer' =>
                $bankTransfer,

            'cheque' =>
                $cheque,

            'online' =>
                $online,

        ];
    }


    /**
     * Outstanding dues report.
     */
    public function dues(array $filters = [])
    {
        $schoolId =
            auth()->user()->school_id;


        return StudentFeeDue::query()

            ->where(
                'student_fee_dues.school_id',
                $schoolId
            )

            ->with([
                'student',
                'feeType',
                'feeStructure',
                'payments',
            ])

            ->when(
                $filters['student_id'] ?? null,
                fn ($query, $studentId) =>
                    $query->where(
                        'student_id',
                        $studentId
                    )
            )

            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) =>
                    $query->where(
                        'status',
                        $status
                    )
            )

            ->latest('id')

            ->get();
    }


    /**
     * Apply payment filters.
     */
    protected function applyPaymentFilters(
        $query,
        array $filters
    ): void {

        if (
            !empty(
                $filters['date_from']
            )
        ) {

            $query->whereDate(
                'payment_date',
                '>=',
                $filters['date_from']
            );

        }


        if (
            !empty(
                $filters['date_to']
            )
        ) {

            $query->whereDate(
                'payment_date',
                '<=',
                $filters['date_to']
            );

        }


        if (
            !empty(
                $filters['payment_method']
            )
        ) {

            $query->where(
                'payment_method',
                $filters['payment_method']
            );

        }


        if (
            !empty(
                $filters['student_id']
            )
        ) {

            $query->where(
                'student_id',
                $filters['student_id']
            );

        }


        if (
            !empty(
                $filters['status']
            )
        ) {

            $query->where(
                'status',
                $filters['status']
            );

        }

    }
}