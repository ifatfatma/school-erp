<?php

namespace App\Services;

use App\Models\BookCopy;
use App\Models\BookIssue;
use App\Repositories\BookReturnRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookReturnService
{
    public function __construct(
        protected BookReturnRepository $repository
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
    | Return Book
    |--------------------------------------------------------------------------
    */

    public function returnBook(array $data)
    {
        $this->validateReturnData($data);

        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Lock Issue
            |--------------------------------------------------------------------------
            */

            $issue = BookIssue::query()
                ->where(
                    'id',
                    $data['book_issue_id']
                )
                ->where(
                    'school_id',
                    auth()->user()->school_id
                )
                ->lockForUpdate()
                ->first();


            if (!$issue) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'Book issue not found.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Already Returned
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $issue->status,
                    [
                        'Returned',
                        'Lost',
                        'Damaged',
                    ],
                    true
                )
            ) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'This book issue has already been closed.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Lock Book Copy
            |--------------------------------------------------------------------------
            */

            $copy = BookCopy::query()
                ->where(
                    'id',
                    $issue->book_copy_id
                )
                ->where(
                    'school_id',
                    auth()->user()->school_id
                )
                ->lockForUpdate()
                ->first();


            if (!$copy) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'Book copy associated with this issue was not found.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Existing Return
            |--------------------------------------------------------------------------
            */

            $existing =
                $this->repository->existingReturn(
                    $issue->id
                );


            if ($existing) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'A return record already exists for this issue.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Return Date
            |--------------------------------------------------------------------------
            */

            $returnDate =
                $data['return_date']
                ?? now()->toDateString();


            /*
            |--------------------------------------------------------------------------
            | Calculate Overdue
            |--------------------------------------------------------------------------
            */

            $dueDate =
                $issue->due_date;


            $returnDateCarbon =
                \Carbon\Carbon::parse(
                    $returnDate
                );


            $dueDateCarbon =
                \Carbon\Carbon::parse(
                    $dueDate
                );


            $overdueDays = 0;


            if (
                $returnDateCarbon->greaterThan(
                    $dueDateCarbon
                )
            ) {

                $overdueDays =
                    $dueDateCarbon->diffInDays(
                        $returnDateCarbon
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Fine
            |--------------------------------------------------------------------------
            */

            $finePerDay =
                $this->finePerDay();


            $fineAmount =
                $overdueDays *
                $finePerDay;


            /*
            |--------------------------------------------------------------------------
            | Fine Status
            |--------------------------------------------------------------------------
            */

            if ($fineAmount > 0) {

                $fineStatus =
                    'Pending';

            } else {

                $fineStatus =
                    'No Fine';
            }


            /*
            |--------------------------------------------------------------------------
            | Book Condition
            |--------------------------------------------------------------------------
            */

            $condition =
                $data['book_condition']
                ?? 'Good';


            /*
            |--------------------------------------------------------------------------
            | Copy Status
            |--------------------------------------------------------------------------
            */

            $copyStatus =
                match ($condition) {

                    'Lost' =>
                        'Lost',

                    'Damaged' =>
                        'Damaged',

                    default =>
                        'Available',

                };


            /*
            |--------------------------------------------------------------------------
            | Issue Status
            |--------------------------------------------------------------------------
            */

            $issueStatus =
                match ($condition) {

                    'Lost' =>
                        'Lost',

                    'Damaged' =>
                        'Damaged',

                    default =>
                        'Returned',

                };


            /*
            |--------------------------------------------------------------------------
            | Create Return
            |--------------------------------------------------------------------------
            */

            $return =
                $this->repository->create([

                    'book_issue_id' =>
                        $issue->id,

                    'library_member_id' =>
                        $issue->library_member_id,

                    'book_id' =>
                        $issue->book_id,

                    'book_copy_id' =>
                        $issue->book_copy_id,

                    'return_no' =>
                        $data['return_no'] ?? null,

                    'return_date' =>
                        $returnDate,

                    'due_date' =>
                        $dueDate,

                    'overdue_days' =>
                        $overdueDays,

                    'fine_amount' =>
                        $fineAmount,

                    'fine_status' =>
                        $data['fine_status']
                        ?? $fineStatus,

                    'book_condition' =>
                        $condition,

                    'remarks' =>
                        $data['remarks']
                        ?? null,

                ]);


            /*
            |--------------------------------------------------------------------------
            | Update Issue
            |--------------------------------------------------------------------------
            */

            $issue->update([

                'return_date' =>
                    $returnDate,

                'status' =>
                    $issueStatus,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Update Book Copy
            |--------------------------------------------------------------------------
            */

            $copy->update([

                'status' =>
                    $copyStatus,

                'updated_by' =>
                    auth()->id(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | Return Complete Data
            |--------------------------------------------------------------------------
            */

            return $return->fresh([

                'bookIssue',

                'libraryMember.student',

                'libraryMember.staff',

                'book',

                'bookCopy',

                'returnedBy',

            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Update Return
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        array $data
    ) {

        $return =
            $this->repository->find($id);


        /*
        |--------------------------------------------------------------------------
        | Do not change core return references
        |--------------------------------------------------------------------------
        */

        unset(
            $data['book_issue_id'],
            $data['library_member_id'],
            $data['book_id'],
            $data['book_copy_id'],
            $data['return_no']
        );


        /*
        |--------------------------------------------------------------------------
        | Recalculate Fine if needed
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['return_date'])
        ) {

            $returnDate =
                \Carbon\Carbon::parse(
                    $data['return_date']
                );


            $dueDate =
                \Carbon\Carbon::parse(
                    $return->due_date
                );


            $overdueDays = 0;


            if (
                $returnDate->greaterThan(
                    $dueDate
                )
            ) {

                $overdueDays =
                    $dueDate->diffInDays(
                        $returnDate
                    );
            }


            $data['overdue_days'] =
                $overdueDays;


            $data['fine_amount'] =
                $overdueDays *
                $this->finePerDay();


            $data['fine_status'] =
                $data['fine_amount'] > 0
                    ? 'Pending'
                    : 'No Fine';
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Condition
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $data['book_condition']
            ) &&
            !in_array(
                $data['book_condition'],
                [
                    'Good',
                    'Damaged',
                    'Lost',
                ],
                true
            )
        ) {

            throw ValidationException::withMessages([
                'book_condition' =>
                    'Invalid book condition.',
            ]);
        }


        return $this->repository->update(
            $id,
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(int $id)
    {
        return $this->repository->delete(
            $id
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
    | Pending Fines
    |--------------------------------------------------------------------------
    */

    public function pendingFines()
    {
        return $this->repository->pendingFines();
    }


    /*
    |--------------------------------------------------------------------------
    | Member Returns
    |--------------------------------------------------------------------------
    */

    public function memberReturns(
        int $memberId
    ) {
        return $this->repository->memberReturns(
            $memberId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Fine Per Day
    |--------------------------------------------------------------------------
    |
    | Later this can be moved to school/library settings.
    |
    */

    protected function finePerDay(): float
    {
        return (float) (
            config(
                'library.fine_per_day',
                5
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

    protected function validateReturnData(
        array $data
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Issue
        |--------------------------------------------------------------------------
        */

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
        | Return Date
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['return_date']
            ) &&
            !strtotime(
                $data['return_date']
            )
        ) {

            throw ValidationException::withMessages([
                'return_date' =>
                    'Invalid return date.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Condition
        |--------------------------------------------------------------------------
        */

        $condition =
            $data['book_condition']
            ?? 'Good';


        if (
            !in_array(
                $condition,
                [
                    'Good',
                    'Damaged',
                    'Lost',
                ],
                true
            )
        ) {

            throw ValidationException::withMessages([
                'book_condition' =>
                    'Invalid book condition.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Fine Status
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $data['fine_status']
            ) &&
            !in_array(
                $data['fine_status'],
                [
                    'No Fine',
                    'Pending',
                    'Paid',
                ],
                true
            )
        ) {

            throw ValidationException::withMessages([
                'fine_status' =>
                    'Invalid fine status.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Remarks
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['remarks']) &&
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