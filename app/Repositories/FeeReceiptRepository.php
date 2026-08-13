<?php

namespace App\Repositories;

use App\Models\FeePayment;

class FeeReceiptRepository
{
    /**
     * Get receipt list.
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

            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'receipt_no',
                            'like',
                            '%' . $search . '%'
                        )

                        ->orWhereHas(
                            'student',
                            function ($student) use ($search) {

                                $student->where(
                                    'admission_no',
                                    'like',
                                    '%' . $search . '%'
                                )

                                ->orWhere(
                                    'first_name',
                                    'like',
                                    '%' . $search . '%'
                                )

                                ->orWhere(
                                    'last_name',
                                    'like',
                                    '%' . $search . '%'
                                );

                            }
                        );

                    });

                }
            )

            ->when(
                $filters['date_from'] ?? null,
                fn ($query, $date) =>
                    $query->whereDate(
                        'payment_date',
                        '>=',
                        $date
                    )
            )

            ->when(
                $filters['date_to'] ?? null,
                fn ($query, $date) =>
                    $query->whereDate(
                        'payment_date',
                        '<=',
                        $date
                    )
            )

            ->when(
                $filters['payment_method'] ?? null,
                fn ($query, $method) =>
                    $query->where(
                        'payment_method',
                        $method
                    )
            )

            ->latest('id')

            ->paginate(
                $filters['per_page'] ?? 20
            );
    }


    /**
     * Get a single receipt.
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
}