<?php

namespace App\Services;

use App\Models\BookIssue;
use App\Models\LibraryFine;
use App\Models\LostDamagedBook;
use App\Repositories\LibraryFineRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LibraryFineService
{
    public function __construct(
        protected LibraryFineRepository $repository
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
    | Create Fine
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $schoolId = auth()->user()->school_id;

            /*
            |--------------------------------------------------------------------------
            | Validate Fine Type
            |--------------------------------------------------------------------------
            */

            if (
                empty($data['fine_type']) ||
                !in_array(
                    $data['fine_type'],
                    [
                        'Overdue',
                        'Lost',
                        'Damaged',
                        'Other',
                    ],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'fine_type' =>
                        'Please select a valid fine type.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Validate Member
            |--------------------------------------------------------------------------
            */

            if (empty($data['library_member_id'])) {
                throw ValidationException::withMessages([
                    'library_member_id' =>
                        'Library member is required.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Get Issue
            |--------------------------------------------------------------------------
            */

            $issue = null;

            if (!empty($data['book_issue_id'])) {

                $issue = BookIssue::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $data['book_issue_id']
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
                | Verify Member
                |--------------------------------------------------------------------------
                */

                if (
                    (int) $issue->library_member_id !==
                    (int) $data['library_member_id']
                ) {
                    throw ValidationException::withMessages([
                        'library_member_id' =>
                            'Selected member does not belong to this issue.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Get Book Information
                |--------------------------------------------------------------------------
                */

                $data['book_id'] =
                    $data['book_id']
                    ?? $issue->book_id;

                $data['book_copy_id'] =
                    $data['book_copy_id']
                    ?? $issue->book_copy_id;
            }


            /*
            |--------------------------------------------------------------------------
            | Lost / Damaged Source
            |--------------------------------------------------------------------------
            */

            if (
                !empty(
                    $data['lost_damaged_book_id']
                )
            ) {

                $lostDamaged =
                    LostDamagedBook::query()
                        ->where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'id',
                            $data['lost_damaged_book_id']
                        )
                        ->first();

                if (!$lostDamaged) {
                    throw ValidationException::withMessages([
                        'lost_damaged_book_id' =>
                            'Lost/damaged record not found.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Automatically determine type
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $lostDamaged->type,
                        [
                            'Lost',
                            'Damaged',
                        ],
                        true
                    )
                ) {
                    $data['fine_type'] =
                        $lostDamaged->type;
                }


                $data['library_member_id'] =
                    $data['library_member_id']
                    ?? $lostDamaged->library_member_id;

                $data['book_id'] =
                    $data['book_id']
                    ?? $lostDamaged->book_id;

                $data['book_copy_id'] =
                    $data['book_copy_id']
                    ?? $lostDamaged->book_copy_id;
            }


            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Fine
            |--------------------------------------------------------------------------
            */

            $duplicateQuery =
                LibraryFine::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->whereNull(
                        'deleted_at'
                    );


            if (
                !empty(
                    $data['lost_damaged_book_id']
                )
            ) {

                $duplicateQuery->where(
                    'lost_damaged_book_id',
                    $data['lost_damaged_book_id']
                );

            } elseif (
                !empty(
                    $data['book_issue_id']
                ) &&
                $data['fine_type'] === 'Overdue'
            ) {

                $duplicateQuery
                    ->where(
                        'book_issue_id',
                        $data['book_issue_id']
                    )
                    ->where(
                        'fine_type',
                        'Overdue'
                    )
                    ->whereIn(
                        'status',
                        [
                            'Pending',
                            'Partially Paid',
                        ]
                    );

            }


            if ($duplicateQuery->exists()) {

                throw ValidationException::withMessages([
                    'fine' =>
                        'A matching active fine already exists.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Fine Date
            |--------------------------------------------------------------------------
            */

            $fineDate =
                !empty($data['fine_date'])
                    ? Carbon::parse(
                        $data['fine_date']
                    )->toDateString()
                    : now()->toDateString();


            /*
            |--------------------------------------------------------------------------
            | Calculate Fine
            |--------------------------------------------------------------------------
            */

            $calculation =
                $this->calculateFine(
                    $data,
                    $issue
                );


            /*
            |--------------------------------------------------------------------------
            | Waiver
            |--------------------------------------------------------------------------
            */

            $fineAmount =
                $calculation['fine_amount'];

            $waiverAmount =
                isset($data['waiver_amount'])
                    ? (float) $data['waiver_amount']
                    : 0;


            if ($waiverAmount < 0) {
                $waiverAmount = 0;
            }


            if ($waiverAmount > $fineAmount) {
                $waiverAmount = $fineAmount;
            }


            /*
            |--------------------------------------------------------------------------
            | Payable
            |--------------------------------------------------------------------------
            */

            $payableAmount =
                max(
                    0,
                    $fineAmount -
                    $waiverAmount
                );


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $status =
                $payableAmount <= 0
                    ? 'Waived'
                    : 'Pending';


            /*
            |--------------------------------------------------------------------------
            | Create
            |--------------------------------------------------------------------------
            */

            return $this->repository->create([

                'library_member_id' =>
                    $data['library_member_id'],

                'book_issue_id' =>
                    $data['book_issue_id']
                    ?? $issue?->id,

                'book_id' =>
                    $data['book_id']
                    ?? null,

                'book_copy_id' =>
                    $data['book_copy_id']
                    ?? null,

                'lost_damaged_book_id' =>
                    $data['lost_damaged_book_id']
                    ?? null,

                'fine_no' =>
                    $data['fine_no']
                    ?? null,

                'fine_date' =>
                    $fineDate,

                'fine_type' =>
                    $data['fine_type'],

                'description' =>
                    $data['description']
                    ?? null,

                'overdue_days' =>
                    $calculation['overdue_days'],

                'fine_per_day' =>
                    $calculation['fine_per_day'],

                'fine_amount' =>
                    $fineAmount,

                'waiver_amount' =>
                    $waiverAmount,

                'payable_amount' =>
                    $payableAmount,

                'status' =>
                    $status,

                'remarks' =>
                    $data['remarks']
                    ?? null,

            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Calculate Fine
    |--------------------------------------------------------------------------
    */

    protected function calculateFine(
        array $data,
        ?BookIssue $issue = null
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Manual Amount
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['fine_amount']) &&
            is_numeric($data['fine_amount'])
        ) {

            return [

                'overdue_days' =>
                    (int) (
                        $data['overdue_days']
                        ?? 0
                    ),

                'fine_per_day' =>
                    (float) (
                        $data['fine_per_day']
                        ?? 0
                    ),

                'fine_amount' =>
                    max(
                        0,
                        (float) $data['fine_amount']
                    ),

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Overdue
        |--------------------------------------------------------------------------
        */

        if (
            $data['fine_type'] ===
            'Overdue'
        ) {

            if (!$issue) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'Book issue is required for an overdue fine.',
                ]);
            }


            if (!$issue->due_date) {

                throw ValidationException::withMessages([
                    'book_issue_id' =>
                        'This issue does not have a due date.',
                ]);
            }


            $dueDate =
                Carbon::parse(
                    $issue->due_date
                )->startOfDay();


            $fineDate =
                !empty($data['fine_date'])
                    ? Carbon::parse(
                        $data['fine_date']
                    )->startOfDay()
                    : now()->startOfDay();


            $overdueDays =
                $dueDate->lt($fineDate)
                    ? $dueDate->diffInDays(
                        $fineDate
                    )
                    : 0;


            /*
            |--------------------------------------------------------------------------
            | Fine Per Day
            |--------------------------------------------------------------------------
            |
            | Replace this with your school's library setting
            | when you have a library settings table.
            |
            */

            $finePerDay =
                isset($data['fine_per_day'])
                    ? (float) $data['fine_per_day']
                    : 5;


            $fineAmount =
                $overdueDays *
                $finePerDay;


            return [

                'overdue_days' =>
                    $overdueDays,

                'fine_per_day' =>
                    $finePerDay,

                'fine_amount' =>
                    round(
                        $fineAmount,
                        2
                    ),

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Lost / Damaged
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $data['fine_type'],
                [
                    'Lost',
                    'Damaged',
                ],
                true
            )
        ) {

            if (
                !empty(
                    $data['lost_damaged_book_id']
                )
            ) {

                $record =
                    LostDamagedBook::query()
                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )
                        ->find(
                            $data['lost_damaged_book_id']
                        );


                if ($record) {

                    return [

                        'overdue_days' =>
                            0,

                        'fine_per_day' =>
                            0,

                        'fine_amount' =>
                            (float)
                            $record->charge_amount,

                    ];
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Manual Lost / Damaged Amount
            |--------------------------------------------------------------------------
            */

            $amount =
                (float) (
                    $data['fine_amount']
                    ?? 0
                );


            return [

                'overdue_days' =>
                    0,

                'fine_per_day' =>
                    0,

                'fine_amount' =>
                    max(
                        0,
                        $amount
                    ),

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Other
        |--------------------------------------------------------------------------
        */

        return [

            'overdue_days' =>
                0,

            'fine_per_day' =>
                0,

            'fine_amount' =>
                max(
                    0,
                    (float) (
                        $data['fine_amount']
                        ?? 0
                    )
                ),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Update Fine
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        array $data
    ) {

        return DB::transaction(
            function () use ($id, $data) {

                $fine =
                    $this->repository->find(
                        $id
                    );


                /*
                |--------------------------------------------------------------------------
                | Cannot change financial values after payment
                |--------------------------------------------------------------------------
                */

                if (
                    $fine->collections()->exists()
                ) {

                    unset(
                        $data['fine_amount'],
                        $data['fine_per_day'],
                        $data['overdue_days']
                    );
                }


                $fineAmount =
                    isset($data['fine_amount'])
                        ? (float) $data['fine_amount']
                        : (float) $fine->fine_amount;


                $waiverAmount =
                    isset($data['waiver_amount'])
                        ? (float) $data['waiver_amount']
                        : (float) $fine->waiver_amount;


                /*
                |--------------------------------------------------------------------------
                | Prevent Excessive Waiver
                |--------------------------------------------------------------------------
                */

                $waiverAmount =
                    min(
                        max(
                            0,
                            $waiverAmount
                        ),
                        $fineAmount
                    );


                $payableAmount =
                    max(
                        0,
                        $fineAmount -
                        $waiverAmount
                    );


                /*
                |--------------------------------------------------------------------------
                | Determine Status
                |--------------------------------------------------------------------------
                */

                $status =
                    $data['status']
                    ?? $fine->status;


                if (
                    $payableAmount <= 0
                ) {

                    $status =
                        'Waived';

                } elseif (
                    $status ===
                    'Waived'
                ) {

                    $status =
                        'Pending';
                }


                $data[
                    'waiver_amount'
                ] =
                    $waiverAmount;


                $data[
                    'payable_amount'
                ] =
                    $payableAmount;


                $data[
                    'status'
                ] =
                    $status;


                return $this->repository->update(
                    $id,
                    $data
                );
            }
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

                $fine =
                    $this->repository->find(
                        $id
                    );


                /*
                |--------------------------------------------------------------------------
                | Payment exists
                |--------------------------------------------------------------------------
                */

                if (
                    $fine->collections()->exists()
                ) {

                    throw ValidationException::withMessages([
                        'fine' =>
                            'A fine with payment records cannot be deleted.',
                    ]);
                }


                return $this->repository->delete(
                    $id
                );
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
    | Pending Fines
    |--------------------------------------------------------------------------
    */

    public function pendingFines()
    {
        return $this->repository->pendingFines();
    }


    /*
    |--------------------------------------------------------------------------
    | Member Fines
    |--------------------------------------------------------------------------
    */

    public function memberFines(
        int $memberId
    ) {
        return $this->repository->memberFines(
            $memberId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Issue Fines
    |--------------------------------------------------------------------------
    */

    public function issueFines(
        int $issueId
    ) {
        return $this->repository->issueFines(
            $issueId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Overdue Fines
    |--------------------------------------------------------------------------
    */

    public function overdueFines()
    {
        return $this->repository->overdueFines();
    }


    /*
    |--------------------------------------------------------------------------
    | Lost / Damaged Fines
    |--------------------------------------------------------------------------
    */

    public function lostDamagedFines()
    {
        return $this->repository->lostDamagedFines();
    }
}