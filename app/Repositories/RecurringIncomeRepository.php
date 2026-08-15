<?php

namespace App\Repositories;

use App\Models\Income;
use App\Models\RecurringIncome;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecurringIncomeRepository
{
    /**
     * List recurring incomes.
     */
    public function list(array $filters = [])
    {
        $schoolId = auth()->user()->school_id;

        $query = RecurringIncome::query()
            ->where(
                'recurring_incomes.school_id',
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

            $query->where(function ($q) use ($search) {

                $q->where(
                    'recurring_incomes.name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'recurring_incomes.reference_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'recurring_incomes.received_from',
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

        if (!empty($filters['income_category_id'])) {

            $query->where(
                'recurring_incomes.income_category_id',
                $filters['income_category_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Frequency
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['frequency'])) {

            $query->where(
                'recurring_incomes.frequency',
                $filters['frequency']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['status'])) {

            $query->where(
                'recurring_incomes.status',
                $filters['status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Auto Create
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['auto_create']) &&
            $filters['auto_create'] !== ''
        ) {

            $query->where(
                'recurring_incomes.auto_create',
                (bool) $filters['auto_create']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Due
        |--------------------------------------------------------------------------
        */

        if (
            !empty($filters['due'])
        ) {

            $query->where(
                'recurring_incomes.status',
                'Active'
            )
            ->where(
                'recurring_incomes.auto_create',
                true
            )
            ->whereDate(
                'recurring_incomes.next_date',
                '<=',
                now()->toDateString()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query
            ->orderBy(
                'recurring_incomes.next_date'
            )
            ->orderByDesc(
                'recurring_incomes.id'
            );

        return $query->paginate(
            (int) (
                $filters['per_page']
                ?? 20
            )
        );
    }


    /**
     * Find recurring income.
     */
    public function find(
        int $id
    ): RecurringIncome {

        return RecurringIncome::query()
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
     * Create recurring income.
     */
    public function create(
        array $data
    ): RecurringIncome {

        $schoolId =
            auth()->user()->school_id;

        $userId =
            auth()->id();

        /*
        |--------------------------------------------------------------------------
        | Calculate next date
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['next_date']
                ?? null
            )
        ) {

            $data['next_date'] =
                $this->calculateNextDate(
                    $data['start_date'],
                    $data['frequency']
                );
        }

        $data['school_id'] =
            $schoolId;

        $data['created_by'] =
            $userId;

        $data['updated_by'] =
            $userId;

        $data['status'] =
            $data['status']
            ?? 'Active';

        $data['auto_create'] =
            $data['auto_create']
            ?? true;

        return RecurringIncome::create(
            $data
        );
    }


    /**
     * Update recurring income.
     */
    public function update(
        int $id,
        array $data
    ): RecurringIncome {

        $recurring =
            $this->find($id);

        /*
        |--------------------------------------------------------------------------
        | Recalculate next date if frequency/start changes
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['next_date']
                ?? null
            )
            &&
            (
                array_key_exists(
                    'start_date',
                    $data
                )
                ||
                array_key_exists(
                    'frequency',
                    $data
                )
            )
        ) {

            $startDate =
                $data['start_date']
                ?? $recurring->start_date;

            $frequency =
                $data['frequency']
                ?? $recurring->frequency;

            $data['next_date'] =
                $this->calculateNextDate(
                    $startDate,
                    $frequency
                );
        }

        $data['updated_by'] =
            auth()->id();

        $recurring->update(
            $data
        );

        return $recurring->fresh([
            'category',
        ]);
    }


    /**
     * Delete recurring income.
     */
    public function delete(
        int $id
    ): bool {

        $recurring =
            $this->find($id);

        return (bool) $recurring->delete();
    }


    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        string $status
    ): RecurringIncome {

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
        ]);
    }


    /**
     * Calculate next occurrence date.
     */
    public function calculateNextDate(
        $date,
        string $frequency
    ): string {

        $date =
            Carbon::parse($date);

        return match ($frequency) {

            'Daily' =>
                $date
                    ->copy()
                    ->addDay()
                    ->format('Y-m-d'),

            'Weekly' =>
                $date
                    ->copy()
                    ->addWeek()
                    ->format('Y-m-d'),

            'Monthly' =>
                $date
                    ->copy()
                    ->addMonthNoOverflow()
                    ->format('Y-m-d'),

            'Quarterly' =>
                $date
                    ->copy()
                    ->addMonthsNoOverflow(3)
                    ->format('Y-m-d'),

            'Yearly' =>
                $date
                    ->copy()
                    ->addYearNoOverflow()
                    ->format('Y-m-d'),

            default =>
                throw ValidationException::withMessages([
                    'frequency' =>
                        'Invalid recurring income frequency.',
                ]),
        };
    }


    /**
     * Generate income from recurring income.
     *
     * This creates an entry in the existing
     * incomes table and then moves next_date
     * to the next occurrence.
     */
    public function generateIncome(
        int $id
    ): Income {

        return DB::transaction(
            function () use ($id) {

                $recurring =
                    RecurringIncome::query()

                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )

                        ->lockForUpdate()

                        ->findOrFail($id);

                /*
                |--------------------------------------------------------------------------
                | Validate status
                |--------------------------------------------------------------------------
                */

                if (
                    $recurring->status !== 'Active'
                ) {

                    throw ValidationException::withMessages([
                        'status' =>
                            'Only active recurring income can generate income.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Validate end date
                |--------------------------------------------------------------------------
                */

                if (
                    $recurring->end_date
                    &&
                    Carbon::parse(
                        $recurring->next_date
                    )->gt(
                        Carbon::parse(
                            $recurring->end_date
                        )
                    )
                ) {

                    $recurring->update([

                        'status' =>
                            'Completed',

                        'updated_by' =>
                            auth()->id(),

                    ]);

                    throw ValidationException::withMessages([
                        'next_date' =>
                            'The recurring income has reached its end date.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent duplicate generation
                |--------------------------------------------------------------------------
                |
                | If the next date is in the future,
                | do not generate manually.
                |
                */

                if (
                    $recurring->next_date
                    &&
                    Carbon::parse(
                        $recurring->next_date
                    )->gt(
                        now()->startOfDay()
                    )
                ) {

                    throw ValidationException::withMessages([
                        'next_date' =>
                            'This recurring income is not due yet.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Create income number
                |--------------------------------------------------------------------------
                */

                $incomeNo =
                    $this->generateIncomeNo(
                        $recurring->school_id
                    );

                /*
                |--------------------------------------------------------------------------
                | Create income
                |--------------------------------------------------------------------------
                */

                $income =
                    Income::create([

                        'school_id' =>
                            $recurring->school_id,

                        'income_category_id' =>
                            $recurring->income_category_id,

                        'income_date' =>
                            $recurring->next_date
                            ??
                            now()->toDateString(),

                        'income_no' =>
                            $incomeNo,

                        'received_from' =>
                            $recurring->received_from,

                        'payment_method' =>
                            $recurring->payment_method,

                        'amount' =>
                            $recurring->amount,

                        'reference_no' =>
                            $recurring->reference_no,

                        'description' =>
                            $recurring->description,

                        'status' =>
                            'Received',

                        'created_by' =>
                            auth()->id(),

                        'updated_by' =>
                            auth()->id(),

                    ]);

                /*
                |--------------------------------------------------------------------------
                | Calculate next occurrence
                |--------------------------------------------------------------------------
                */

                $currentDate =
                    $recurring->next_date
                    ??
                    now()->toDateString();

                $nextDate =
                    $this->calculateNextDate(
                        $currentDate,
                        $recurring->frequency
                    );

                /*
                |--------------------------------------------------------------------------
                | Check end date
                |--------------------------------------------------------------------------
                */

                if (
                    $recurring->end_date
                    &&
                    Carbon::parse(
                        $nextDate
                    )->gt(
                        Carbon::parse(
                            $recurring->end_date
                        )
                    )
                ) {

                    $recurring->update([

                        'next_date' =>
                            null,

                        'status' =>
                            'Completed',

                        'updated_by' =>
                            auth()->id(),

                    ]);

                } else {

                    $recurring->update([

                        'next_date' =>
                            $nextDate,

                        'updated_by' =>
                            auth()->id(),

                    ]);
                }

                return $income->fresh([
                    'category',
                ]);
            }
        );
    }


    /**
     * Generate income number.
     */
    protected function generateIncomeNo(
        int $schoolId
    ): string {

        $year =
            now()->format('Y');

        $last =
            Income::query()

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

        if (
            $last
            &&
            preg_match(
                '/(\d+)$/',
                $last->income_no,
                $matches
            )
        ) {

            $number =
                ((int) $matches[1]) + 1;
        }

        return sprintf(
            'INC-%s-%06d',
            $year,
            $number
        );
    }


    /**
     * Generate all due recurring incomes.
     *
     * Useful later for a scheduler/command.
     */
    public function generateDue(): int
    {
        $schoolId =
            auth()->user()->school_id;

        $recurringIncomes =
            RecurringIncome::query()

                ->where(
                    'school_id',
                    $schoolId
                )

                ->where(
                    'status',
                    'Active'
                )

                ->where(
                    'auto_create',
                    true
                )

                ->whereNotNull(
                    'next_date'
                )

                ->whereDate(
                    'next_date',
                    '<=',
                    now()->toDateString()
                )

                ->pluck('id');

        $count = 0;

        foreach (
            $recurringIncomes as $id
        ) {

            try {

                $this->generateIncome(
                    $id
                );

                $count++;

            } catch (
                ValidationException $e
            ) {

                /*
                 * Continue processing other
                 * recurring income records.
                 */

                continue;
            }
        }

        return $count;
    }
}