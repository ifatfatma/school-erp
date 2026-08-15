<?php

namespace App\Repositories;

use App\Models\Expense;

class ExpenseRepository
{
    /**
     * List expenses.
     */
    public function list(
        array $filters = []
    ) {
        return Expense::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->with([
                'category',
                'creator',
                'updater',
            ])

            ->filter($filters)

            ->latest(
                'expense_date'
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
     * Find expense.
     */
    public function find(
        int $id
    ): Expense {

        return Expense::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->with([
                'category',
                'creator',
                'updater',
            ])

            ->findOrFail($id);
    }


    /**
     * Create expense.
     */
    public function create(
        array $data
    ): Expense {

        $data['school_id'] =
            auth()->user()->school_id;

        $data['created_by'] =
            auth()->id();

        $data['updated_by'] =
            auth()->id();


        if (
            empty(
                $data['expense_no']
                ?? null
            )
        ) {

            $data['expense_no'] =
                $this->generateExpenseNo(
                    $data['school_id']
                );
        }


        return Expense::create(
            $data
        );
    }


    /**
     * Update expense.
     */
    public function update(
        int $id,
        array $data
    ): Expense {

        $expense =
            $this->find($id);

        $data['updated_by'] =
            auth()->id();


        $expense->update(
            $data
        );


        return $expense->fresh([
            'category',
            'creator',
            'updater',
        ]);
    }


    /**
     * Delete expense.
     */
    public function delete(
        int $id
    ): bool {

        return $this
            ->find($id)
            ->delete();
    }


    /**
     * Toggle status.
     */
    public function toggleStatus(
        int $id
    ): Expense {

        $expense =
            $this->find($id);


        $nextStatus =
            match ($expense->status) {

                'Paid' =>
                    'Cancelled',

                'Cancelled' =>
                    'Pending',

                'Pending' =>
                    'Paid',

                default =>
                    'Paid',

            };


        $expense->update([

            'status' =>
                $nextStatus,

            'updated_by' =>
                auth()->id(),

        ]);


        return $expense->fresh([
            'category',
            'creator',
            'updater',
        ]);
    }


    /**
     * Generate expense number.
     */
    protected function generateExpenseNo(
        int $schoolId
    ): string {

        $year =
            now()->format('Y');


        $lastExpense =
            Expense::query()

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


        if ($lastExpense) {

            if (
                preg_match(
                    '/(\d+)$/',
                    $lastExpense->expense_no,
                    $matches
                )
            ) {

                $number =
                    ((int) $matches[1]) + 1;
            }
        }


        return sprintf(
            'EXP-%s-%06d',
            $year,
            $number
        );
    }


    /**
     * Get total expense.
     */
    public function total(
        array $filters = []
    ): float {

        return (float) Expense::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->filter($filters)

            ->where(
                'status',
                'Paid'
            )

            ->sum('amount');
    }
}