<?php

namespace App\Services;

use App\Models\BookCopy;
use App\Models\BookIssue;
use App\Models\LostDamagedBook;
use App\Repositories\LostDamagedBookRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LostDamagedBookService
{
    public function __construct(
        protected LostDamagedBookRepository $repository
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
    | Create Lost / Damaged Report
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {
        $this->validateData($data);

        return DB::transaction(function () use ($data) {

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
                        auth()->user()->school_id
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
            }


            /*
            |--------------------------------------------------------------------------
            | Get Book Copy
            |--------------------------------------------------------------------------
            */

            $copy = BookCopy::query()
                ->where(
                    'school_id',
                    auth()->user()->school_id
                )
                ->where(
                    'id',
                    $data['book_copy_id']
                )
                ->lockForUpdate()
                ->first();


            if (!$copy) {

                throw ValidationException::withMessages([
                    'book_copy_id' =>
                        'Book copy not found.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Verify Book
            |--------------------------------------------------------------------------
            */

            if (
                !empty($data['book_id']) &&
                (int) $copy->book_id !==
                    (int) $data['book_id']
            ) {

                throw ValidationException::withMessages([
                    'book_id' =>
                        'Selected book does not belong to the selected book copy.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Active Report
            |--------------------------------------------------------------------------
            */

            $existing = LostDamagedBook::query()
                ->where(
                    'school_id',
                    auth()->user()->school_id
                )
                ->where(
                    'book_copy_id',
                    $copy->id
                )
                ->whereIn(
                    'charge_status',
                    [
                        'Pending',
                        'Paid',
                    ]
                )
                ->whereNull(
                    'deleted_at'
                )
                ->latest('id')
                ->first();


            if ($existing) {

                throw ValidationException::withMessages([
                    'book_copy_id' =>
                        'A lost/damaged report already exists for this book copy.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Check Copy Status
            |--------------------------------------------------------------------------
            */

            $currentStatus =
                strtolower(
                    trim(
                        (string) (
                            $copy->status ?? ''
                        )
                    )
                );


            if (
                in_array(
                    $currentStatus,
                    [
                        'lost',
                        'damaged'
                    ],
                    true
                )
            ) {

                throw ValidationException::withMessages([
                    'book_copy_id' =>
                        'This book copy is already marked as ' .
                        ucfirst($currentStatus) .
                        '.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Determine Book ID
            |--------------------------------------------------------------------------
            */

            $bookId =
                $data['book_id']
                ?? $copy->book_id;


            /*
            |--------------------------------------------------------------------------
            | Determine Member
            |--------------------------------------------------------------------------
            */

            $memberId =
                $data['library_member_id']
                ?? $issue?->library_member_id;


            if (!$memberId) {

                throw ValidationException::withMessages([
                    'library_member_id' =>
                        'Library member is required.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Issue / Copy Consistency
            |--------------------------------------------------------------------------
            */

            if ($issue) {

                if (
                    (int) $issue->book_copy_id !==
                    (int) $copy->id
                ) {

                    throw ValidationException::withMessages([
                        'book_copy_id' =>
                            'Selected book copy does not belong to this issue.',
                    ]);
                }


                if (
                    (int) $issue->book_id !==
                    (int) $bookId
                ) {

                    throw ValidationException::withMessages([
                        'book_id' =>
                            'Selected book does not belong to this issue.',
                    ]);
                }


                if (
                    (int) $issue->library_member_id !==
                    (int) $memberId
                ) {

                    throw ValidationException::withMessages([
                        'library_member_id' =>
                            'Selected member does not belong to this issue.',
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Report Type
            |--------------------------------------------------------------------------
            */

            $type =
                $data['type'];


            /*
            |--------------------------------------------------------------------------
            | Book Value
            |--------------------------------------------------------------------------
            */

            $bookValue =
                isset($data['book_value'])
                    ? (float) $data['book_value']
                    : $this->getBookValue(
                        $copy,
                        $bookId
                    );


            /*
            |--------------------------------------------------------------------------
            | Charge
            |--------------------------------------------------------------------------
            */

            $chargeAmount =
                isset($data['charge_amount'])
                    ? (float) $data['charge_amount']
                    : $this->calculateCharge(
                        $type,
                        $bookValue
                    );


            /*
            |--------------------------------------------------------------------------
            | Charge Status
            |--------------------------------------------------------------------------
            */

            $chargeStatus =
                $chargeAmount > 0
                    ? 'Pending'
                    : 'Waived';


            /*
            |--------------------------------------------------------------------------
            | Resolution
            |--------------------------------------------------------------------------
            */

            $resolution =
                'Pending';


            /*
            |--------------------------------------------------------------------------
            | Report Date
            |--------------------------------------------------------------------------
            */

            $reportDate =
                !empty($data['report_date'])
                    ? Carbon::parse(
                        $data['report_date']
                    )->toDateString()
                    : now()->toDateString();


            /*
            |--------------------------------------------------------------------------
            | Create Report
            |--------------------------------------------------------------------------
            */

            $record =
                $this->repository->create([

                    'book_issue_id' =>
                        $issue?->id,

                    'library_member_id' =>
                        $memberId,

                    'book_id' =>
                        $bookId,

                    'book_copy_id' =>
                        $copy->id,

                    'report_no' =>
                        $data['report_no'] ??
                        null,

                    'report_date' =>
                        $reportDate,

                    'type' =>
                        $type,

                    'description' =>
                        $data['description'] ??
                        null,

                    'book_value' =>
                        $bookValue,

                    'charge_amount' =>
                        $chargeAmount,

                    'charge_status' =>
                        $data['charge_status'] ??
                        $chargeStatus,

                    'resolution' =>
                        $data['resolution'] ??
                        $resolution,

                    'resolved_date' =>
                        $data['resolved_date'] ??
                        null,

                    'remarks' =>
                        $data['remarks'] ??
                        null,

                ]);


            /*
            |--------------------------------------------------------------------------
            | Update Book Copy
            |--------------------------------------------------------------------------
            */

            $copyStatus =
                $type === 'Lost'
                    ? 'Lost'
                    : 'Damaged';


            $copy->update([
                'status' =>
                    $copyStatus,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Update Book Issue
            |--------------------------------------------------------------------------
            */

            if ($issue) {

                $issue->update([
                    'status' =>
                        $type === 'Lost'
                            ? 'Lost'
                            : 'Damaged',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Return Record
            |--------------------------------------------------------------------------
            */

            return $record->fresh([
                'bookIssue',
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'reportedBy',
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
        return DB::transaction(
            function () use ($id, $data) {

                $record =
                    $this->repository->find(
                        $id
                    );


                /*
                |--------------------------------------------------------------------------
                | Financial Fields
                |--------------------------------------------------------------------------
                |
                | Charge status and resolution can be updated here.
                |
                */

                $allowed = [
                    'description',
                    'charge_amount',
                    'charge_status',
                    'resolution',
                    'resolved_date',
                    'remarks',
                ];


                $update = [];


                foreach ($allowed as $field) {

                    if (
                        array_key_exists(
                            $field,
                            $data
                        )
                    ) {

                        $update[$field] =
                            $data[$field];
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Automatically Resolve
                |--------------------------------------------------------------------------
                */

                if (
                    isset(
                        $update['charge_status']
                    ) &&
                    $update['charge_status'] === 'Paid'
                ) {

                    $update['resolution'] =
                        'Payment';

                    $update['resolved_date'] =
                        $update['resolved_date']
                        ?? now()->toDateString();
                }


                if (
                    isset(
                        $update['charge_status']
                    ) &&
                    $update['charge_status'] === 'Waived'
                ) {

                    $update['resolution'] =
                        'Waived';

                    $update['resolved_date'] =
                        $update['resolved_date']
                        ?? now()->toDateString();
                }


                /*
                |--------------------------------------------------------------------------
                | Replacement
                |--------------------------------------------------------------------------
                */

                if (
                    isset(
                        $update['resolution']
                    ) &&
                    $update['resolution'] ===
                    'Replacement'
                ) {

                    $update['resolved_date'] =
                        $update['resolved_date']
                        ?? now()->toDateString();
                }


                return $this->repository->update(
                    $id,
                    $update
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

                $record =
                    $this->repository->find(
                        $id
                    );


                /*
                |--------------------------------------------------------------------------
                | Don't delete paid records
                |--------------------------------------------------------------------------
                */

                if (
                    $record->charge_status ===
                    'Paid'
                ) {

                    throw ValidationException::withMessages([
                        'record' =>
                            'A paid lost/damaged record cannot be deleted.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Restore Book Copy Status
                |--------------------------------------------------------------------------
                |
                | Only restore if the copy is still carrying
                | the status created by this report.
                |
                */

                $copy =
                    BookCopy::query()
                        ->where(
                            'school_id',
                            auth()->user()->school_id
                        )
                        ->where(
                            'id',
                            $record->book_copy_id
                        )
                        ->lockForUpdate()
                        ->first();


                if ($copy) {

                    $copy->update([
                        'status' =>
                            'Available',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Restore Issue Status
                |--------------------------------------------------------------------------
                */

                if (
                    $record->book_issue_id
                ) {

                    $issue =
                        BookIssue::query()
                            ->where(
                                'school_id',
                                auth()->user()->school_id
                            )
                            ->where(
                                'id',
                                $record->book_issue_id
                            )
                            ->first();


                    if ($issue) {

                        $issue->update([
                            'status' =>
                                'Issued',
                        ]);
                    }
                }


                return $record->delete();
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Calculate Charge
    |--------------------------------------------------------------------------
    */

    protected function calculateCharge(
        string $type,
        float $bookValue
    ): float {

        /*
         * Lost:
         * Full book value.
         *
         * Damaged:
         * Default 50% of book value.
         */

        if ($type === 'Lost') {

            return round(
                $bookValue,
                2
            );
        }


        return round(
            $bookValue * 0.50,
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Book Value
    |--------------------------------------------------------------------------
    */

    protected function getBookValue(
        BookCopy $copy,
        int $bookId
    ): float {

        /*
         * First try the copy's price/value fields.
         */

        foreach ([
            'book_value',
            'purchase_price',
            'price',
        ] as $field) {

            if (
                isset(
                    $copy->{$field}
                ) &&
                (float) $copy->{$field} > 0
            ) {

                return (float) $copy->{$field};
            }
        }


        /*
         * Then try the Book model.
         */

        $book =
            $copy->book;


        if ($book) {

            foreach ([
                'book_value',
                'purchase_price',
                'price',
            ] as $field) {

                if (
                    isset(
                        $book->{$field}
                    ) &&
                    (float) $book->{$field} > 0
                ) {

                    return (float) $book->{$field};
                }
            }
        }


        return 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function validateData(
        array $data
    ): void {

        if (
            empty(
                $data['type']
            )
        ) {

            throw ValidationException::withMessages([
                'type' =>
                    'Report type is required.',
            ]);
        }


        if (
            !in_array(
                $data['type'],
                [
                    'Lost',
                    'Damaged',
                ],
                true
            )
        ) {

            throw ValidationException::withMessages([
                'type' =>
                    'Invalid report type.',
            ]);
        }


        if (
            empty(
                $data['book_copy_id']
            )
        ) {

            throw ValidationException::withMessages([
                'book_copy_id' =>
                    'Book copy is required.',
            ]);
        }


        if (
            empty(
                $data['library_member_id']
            ) &&
            empty(
                $data['book_issue_id']
            )
        ) {

            throw ValidationException::withMessages([
                'library_member_id' =>
                    'Library member is required.',
            ]);
        }


        if (
            isset(
                $data['book_value']
            ) &&
            (
                !is_numeric(
                    $data['book_value']
                ) ||
                (float) $data['book_value'] < 0
            )
        ) {

            throw ValidationException::withMessages([
                'book_value' =>
                    'Book value must be a valid positive amount.',
            ]);
        }


        if (
            isset(
                $data['charge_amount']
            ) &&
            (
                !is_numeric(
                    $data['charge_amount']
                ) ||
                (float) $data['charge_amount'] < 0
            )
        ) {

            throw ValidationException::withMessages([
                'charge_amount' =>
                    'Charge amount must be a valid positive amount.',
            ]);
        }


        if (
            !empty(
                $data['report_date']
            ) &&
            !strtotime(
                $data['report_date']
            )
        ) {

            throw ValidationException::withMessages([
                'report_date' =>
                    'Invalid report date.',
            ]);
        }


        if (
            !empty(
                $data['resolved_date']
            ) &&
            !strtotime(
                $data['resolved_date']
            )
        ) {

            throw ValidationException::withMessages([
                'resolved_date' =>
                    'Invalid resolved date.',
            ]);
        }


        if (
            !empty(
                $data['description']
            ) &&
            mb_strlen(
                $data['description']
            ) > 10000
        ) {

            throw ValidationException::withMessages([
                'description' =>
                    'Description cannot exceed 10000 characters.',
            ]);
        }


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