<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Models\RecurringExpense;
use Illuminate\Support\Facades\DB;

class RecurringExpenseRepository
{
    /**
     * List recurring expenses.
     */
    public function list(
        array $filters = []
    ) {
        return RecurringExpense::query()

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

            ->orderBy(
                'next_due_date'
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
     * Find recurring expense.
     */
    public function find(
        int $id
    ): RecurringExpense {

        return RecurringExpense::query()

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
     * Create recurring expense.
     */
    public function create(
        array $data
    ): RecurringExpense {

        $data['school_id'] =
            auth()->user()->school_id;

        $data['created_by'] =
            auth()->id();

        $data['updated_by'] =
            auth()->id();


        return RecurringExpense::create(
            $data
        );
    }


    /**
     * Update recurring expense.
     */
    public function update(
        int $id,
        array $data
    ): RecurringExpense {

        $recurring =
            $this->find($id);


        $data['updated_by'] =
            auth()->id();


        $recurring->update(
            $data
        );


        return $recurring->fresh([
            'category',
            'creator',
            'updater',
        ]);
    }


    /**
     * Delete recurring expense.
     */
    public function delete(
        int $id
    ): bool {

        return $this
            ->find($id)
            ->delete();
    }


    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        string $status
    ): RecurringExpense {

        $recurring =
            $this->find($id);


        $recurring->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);


        return $recurring->fresh([
            'category',
            'creator',
            'updater',
        ]);
    }


    /**
     * Toggle auto create.
     */
    public function toggleAutoCreate(
        int $id
    ): RecurringExpense {

        $recurring =
            $this->find($id);


        $recurring->update([

            'auto_create' =>
                !$recurring->auto_create,

            'updated_by' =>
                auth()->id(),

        ]);


        return $recurring->fresh([
            'category',
            'creator',
            'updater',
        ]);
    }


    /**
     * Generate an expense from a recurring expense.
     */
    public function generateExpense(
        int $id
    ): Expense {

        return DB::transaction(
            function () use ($id) {

                $recurring =
                    RecurringExpense::query()

                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )

                        ->lockForUpdate()

                        ->findOrFail($id);


                $expense =
                    Expense::create([

                        'school_id' =>
                            $recurring->school_id,

                        'expense_category_id' =>
                            $recurring->expense_category_id,

                        'expense_date' =>
                            now()->toDateString(),

                        'payee_name' =>
                            $recurring->payee_name,

                        'payment_method' =>
                            $recurring->payment_method,

                        'amount' =>
                            $recurring->amount,

                        'description' =>
                            $recurring->description,

                        'status' =>
                            'Pending',

                        'created_by' =>
                            auth()->id(),

                        'updated_by' =>
                            auth()->id(),

                    ]);


                /*
                 * Calculate next due date.
                 */
                $nextDate =
                    $recurring->next_due_date
                        ? $recurring->next_due_date->copy()
                        : now();


                switch (
                    $recurring->frequency
                ) {

                    case 'Daily':

                        $nextDate->addDay();

                        break;


                    case 'Weekly':

                        $nextDate->addWeek();

                        break;


                    case 'Monthly':

                        $nextDate->addMonth();

                        break;


                    case 'Quarterly':

                        $nextDate->addMonths(3);

                        break;


                    case 'Yearly':

                        $nextDate->addYear();

                        break;

                }


                /*
                 * If end date has passed,
                 * mark as completed.
                 */
                if (
                    $recurring->end_date &&
                    $nextDate->greaterThan(
                        $recurring->end_date
                    )
                ) {

                    $recurring->update([

                        'next_due_date' =>
                            $nextDate,

                        'status' =>
                            'Completed',

                        'updated_by' =>
                            auth()->id(),

                    ]);

                } else {

                    $recurring->update([

                        'next_due_date' =>
                            $nextDate,

                        'updated_by' =>
                            auth()->id(),

                    ]);

                }


                return $expense->load([
                    'category',
                ]);
            }
        );
    }
}