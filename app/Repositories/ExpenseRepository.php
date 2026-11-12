<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Services\TenantManager;

class ExpenseRepository
{
    protected function resolveSchoolId(?int $filterSchoolId = null): ?int
    {
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);

        if (!$tenantManager->isSuperAdmin($user)) {
            return $user?->school_id ? (int) $user->school_id : null;
        }

        if ($filterSchoolId !== null && $filterSchoolId > 0) {
            return (int) $filterSchoolId;
        }

        $tenantId = $tenantManager->getTenantId();
        if ($tenantId) {
            return (int) $tenantId;
        }

        return $user?->school_id ? (int) $user->school_id : null;
    }

    /**
     * List expenses.
     */
    public function list(
        array $filters = []
    ) {
        $filterSchoolId = !empty($filters['school_id']) && $filters['school_id'] !== 'all' ? (int) $filters['school_id'] : null;
        $schoolId = $this->resolveSchoolId($filterSchoolId);

        $query = Expense::query()
            ->with([
                'category',
                'creator',
                'updater',
                'school:id,name,code',
            ]);

        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        }

        return $query
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
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);
        $isSuperAdmin = $tenantManager->isSuperAdmin($user);

        $query = Expense::query()
            ->with([
                'category',
                'creator',
                'updater',
                'school:id,name,code',
            ]);

        if (!$isSuperAdmin && $user?->school_id) {
            $query->where('school_id', $user->school_id);
        }

        return $query->findOrFail($id);
    }

    /**
     * Create expense.
     */
    public function create(
        array $data
    ): Expense {
        $data['school_id'] = $data['school_id'] ?? $this->resolveSchoolId() ?? \App\Models\School::first()?->id ?? 1;

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
        $filterSchoolId = !empty($filters['school_id']) && $filters['school_id'] !== 'all' ? (int) $filters['school_id'] : null;
        $schoolId = $this->resolveSchoolId($filterSchoolId);

        $query = Expense::query();

        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        }

        return (float) $query
            ->filter($filters)
            ->where(
                'status',
                'Paid'
            )
            ->sum('amount');
    }
}