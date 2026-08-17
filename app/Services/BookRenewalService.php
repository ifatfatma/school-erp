<?php

namespace App\Services;

use App\Models\BookIssue;
use App\Repositories\BookRenewalRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookRenewalService
{
    public function __construct(
        protected BookRenewalRepository $repository
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }


    /*
    |--------------------------------------------------------------------------
    | Find
    |--------------------------------------------------------------------------
    */

    public function find(int $id)
    {
        return $this->repository->find($id);
    }


    /*
    |--------------------------------------------------------------------------
    | Renew Book
    |--------------------------------------------------------------------------
    */

    public function renew(array $data)
    {
        $this->validateRenewalData($data);

        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Lock Active Issue
            |--------------------------------------------------------------------------
            */

            $issue =
                $this->repository->findActiveIssue(
                    (int) $data['book_issue_id']
                );


            if (!$issue) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'Active book issue not found.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Existing Renewal Count
            |--------------------------------------------------------------------------
            */

            $renewalCount =
                $this->repository->renewalCount(
                    $issue->id
                );


            /*
            |--------------------------------------------------------------------------
            | Maximum Renewal Limit
            |--------------------------------------------------------------------------
            |
            | Default: 2 renewals.
            |
            */

            $maxRenewals =
                $this->maxRenewals();


            if (
                $renewalCount >=
                $maxRenewals
            ) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        "This book has already been renewed {$renewalCount} time(s). Maximum allowed renewals are {$maxRenewals}.",
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Member Validation
            |--------------------------------------------------------------------------
            */

            $member =
                $issue->libraryMember;


            if (!$member) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'Library member associated with this issue was not found.',
                ]);
            }


            if (!$member->status) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'Library member is inactive.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Membership Expiry
            |--------------------------------------------------------------------------
            */

            if (
                $member->expiry_date &&
                Carbon::parse(
                    $member->expiry_date
                )->isPast()
            ) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'Library membership has expired.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Overdue Restriction
            |--------------------------------------------------------------------------
            */

            if (
                $this->blockOverdueRenewal() &&
                $this->isOverdue($issue)
            ) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'Overdue books cannot be renewed. Please return the book first.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Existing Pending Fine
            |--------------------------------------------------------------------------
            */

            if (
                $this->blockFineRenewal() &&
                $this->hasPendingFine(
                    $issue
                )
            ) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'This member has a pending library fine and cannot renew the book.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Old Due Date
            |--------------------------------------------------------------------------
            */

            $oldDueDate =
                Carbon::parse(
                    $issue->due_date
                );


            /*
            |--------------------------------------------------------------------------
            | Renewal Date
            |--------------------------------------------------------------------------
            */

            $renewalDate =
                !empty(
                    $data['renewal_date']
                )
                    ? Carbon::parse(
                        $data['renewal_date']
                    )
                    : now()->startOfDay();


            /*
            |--------------------------------------------------------------------------
            | Renewal Period
            |--------------------------------------------------------------------------
            */

            $renewalDays =
                isset(
                    $data['renewal_days']
                )
                    ? (int) $data['renewal_days']
                    : $this->renewalDays();


            if (
                $renewalDays <= 0
            ) {

                throw ValidationException::withMessages([
                    'renewal_days' =>
                        'Renewal period must be greater than zero.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | New Due Date
            |--------------------------------------------------------------------------
            */

            $newDueDate =
                $oldDueDate->copy()
                    ->addDays(
                        $renewalDays
                    );


            /*
            |--------------------------------------------------------------------------
            | Optional Maximum Due Date
            |--------------------------------------------------------------------------
            */

            $maximumDueDate =
                $this->maximumDueDate();


            if (
                $maximumDueDate &&
                $newDueDate->greaterThan(
                    $maximumDueDate
                )
            ) {

                throw ValidationException::withMessages([
                    'renewal_days' =>
                        'The calculated new due date exceeds the maximum allowed library due date.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Renewal Count
            |--------------------------------------------------------------------------
            */

            $newRenewalCount =
                $renewalCount + 1;


            /*
            |--------------------------------------------------------------------------
            | Create Renewal
            |--------------------------------------------------------------------------
            */

            $renewal =
                $this->repository->create([

                    'book_issue_id' =>
                        $issue->id,

                    'library_member_id' =>
                        $issue->library_member_id,

                    'book_id' =>
                        $issue->book_id,

                    'book_copy_id' =>
                        $issue->book_copy_id,

                    'renewal_no' =>
                        $data['renewal_no'] ??
                        null,

                    'renewal_date' =>
                        $renewalDate
                            ->toDateString(),

                    'old_due_date' =>
                        $oldDueDate
                            ->toDateString(),

                    'new_due_date' =>
                        $newDueDate
                            ->toDateString(),

                    'renewal_count' =>
                        $newRenewalCount,

                    'remarks' =>
                        $data['remarks'] ??
                        null,

                ]);


            /*
            |--------------------------------------------------------------------------
            | Update Original Issue
            |--------------------------------------------------------------------------
            */

            $issue->update([

                'due_date' =>
                    $newDueDate
                        ->toDateString(),

                /*
                 * If it was marked overdue previously,
                 * make it active again after renewal.
                 */
                'status' =>
                    $issue->status === 'Overdue'
                        ? 'Issued'
                        : $issue->status,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Return Renewal
            |--------------------------------------------------------------------------
            */

            return $renewal->fresh([

                'bookIssue',

                'libraryMember.student',

                'libraryMember.staff',

                'book',

                'bookCopy',

                'renewedBy',

            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        array $data
    ) {
        $renewal =
            $this->repository->find(
                $id
            );


        /*
        |--------------------------------------------------------------------------
        | Renewal history should remain immutable
        |--------------------------------------------------------------------------
        |
        | Only remarks can safely be changed.
        |
        */

        $update = [];


        if (
            array_key_exists(
                'remarks',
                $data
            )
        ) {

            $update['remarks'] =
                $data['remarks'];
        }


        return $this->repository->update(
            $id,
            $update
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(int $id)
    {
        return DB::transaction(
            function () use ($id) {

                $renewal =
                    $this->repository->find(
                        $id
                    );


                /*
                 * Do not delete renewal history
                 * by default.
                 */

                throw ValidationException::withMessages([
                    'renewal' =>
                        'Renewal history cannot be deleted.',
                ]);
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    public function statistics()
    {
        return $this->repository->statistics();
    }


    /*
    |--------------------------------------------------------------------------
    | Member Renewals
    |--------------------------------------------------------------------------
    */

    public function memberRenewals(
        int $memberId
    ) {
        return $this->repository->memberRenewals(
            $memberId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Issue Renewals
    |--------------------------------------------------------------------------
    */

    public function issueRenewals(
        int $issueId
    ) {
        return $this->repository->issueRenewals(
            $issueId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Maximum Renewals
    |--------------------------------------------------------------------------
    */

    protected function maxRenewals(): int
    {
        return (int) config(
            'library.max_renewals',
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Renewal Days
    |--------------------------------------------------------------------------
    */

    protected function renewalDays(): int
    {
        return (int) config(
            'library.renewal_days',
            14
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Block Overdue
    |--------------------------------------------------------------------------
    */

    protected function blockOverdueRenewal(): bool
    {
        return (bool) config(
            'library.block_overdue_renewal',
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Block Pending Fine
    |--------------------------------------------------------------------------
    */

    protected function blockFineRenewal(): bool
    {
        return (bool) config(
            'library.block_fine_renewal',
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Maximum Due Date
    |--------------------------------------------------------------------------
    */

    protected function maximumDueDate(): ?Carbon
    {
        $days =
            config(
                'library.maximum_renewal_days',
                null
            );


        if (
            !$days ||
            (int) $days <= 0
        ) {

            return null;
        }


        return now()
            ->startOfDay()
            ->addDays(
                (int) $days
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Check Overdue
    |--------------------------------------------------------------------------
    */

    protected function isOverdue(
        BookIssue $issue
    ): bool {

        if (!$issue->due_date) {
            return false;
        }


        return Carbon::parse(
            $issue->due_date
        )->startOfDay()->isBefore(
            now()->startOfDay()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Check Pending Fine
    |--------------------------------------------------------------------------
    */

    protected function hasPendingFine(
        BookIssue $issue
    ): bool {

        /*
         * Keep this query independent from
         * BookReturnRepository to avoid
         * unnecessary service dependencies.
         */

        return \App\Models\BookReturn::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'book_issue_id',
                $issue->id
            )
            ->where(
                'fine_status',
                'Pending'
            )
            ->where(
                'fine_amount',
                '>',
                0
            )
            ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

    protected function validateRenewalData(
        array $data
    ): void {

        if (
            empty(
                $data['book_issue_id']
            )
        ) {

            throw ValidationException::withMessages([
                'book_issue_id' =>
                    'Book issue is required.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Renewal Date
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['renewal_date']
            ) &&
            !strtotime(
                $data['renewal_date']
            )
        ) {

            throw ValidationException::withMessages([
                'renewal_date' =>
                    'Invalid renewal date.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Renewal Days
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $data['renewal_days']
            )
        ) {

            if (
                !is_numeric(
                    $data['renewal_days']
                ) ||
                (int) $data['renewal_days'] <= 0
            ) {

                throw ValidationException::withMessages([
                    'renewal_days' =>
                        'Renewal days must be greater than zero.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Renewal Number
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['renewal_no']
            ) &&
            strlen(
                $data['renewal_no']
            ) > 255
        ) {

            throw ValidationException::withMessages([
                'renewal_no' =>
                    'Renewal number cannot exceed 255 characters.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Remarks
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['remarks']
            ) &&
            mb_strlen(
                $data['remarks']
            ) > 10000
        ) {

            throw ValidationException::withMessages([
                'remarks' =>
                    'Remarks cannot exceed 10000 characters.',
            ]);
        }
    }
}