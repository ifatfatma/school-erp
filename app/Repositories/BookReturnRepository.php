<?php

namespace App\Repositories;

use App\Models\BookReturn;
use App\Models\BookIssue;
use Illuminate\Validation\ValidationException;

class BookReturnRepository
{
    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    protected function query()
    {
        return BookReturn::query()
            ->where(
                'book_returns.school_id',
                auth()->user()->school_id
            );
    }


    /*
    |--------------------------------------------------------------------------
    | List Returns
    |--------------------------------------------------------------------------
    */

    public function list(array $filters = [])
    {
        $query = $this->query()
            ->with([
                'bookIssue',
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'returnedBy',
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
                    'book_returns.return_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'bookIssue',
                    function ($issue) use ($search) {

                        $issue->where(
                            'issue_no',
                            'like',
                            "%{$search}%"
                        );
                    }
                )

                ->orWhereHas(
                    'libraryMember',
                    function ($member) use ($search) {

                        $member->where(
                            'member_no',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'phone',
                            'like',
                            "%{$search}%"
                        );
                    }
                )

                ->orWhereHas(
                    'book',
                    function ($book) use ($search) {

                        $book->where(
                            'title',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'isbn',
                            'like',
                            "%{$search}%"
                        );
                    }
                )

                ->orWhereHas(
                    'bookCopy',
                    function ($copy) use ($search) {

                        $copy->where(
                            'accession_no',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'barcode',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Member
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['library_member_id'])) {

            $query->where(
                'book_returns.library_member_id',
                $filters['library_member_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Book
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['book_id'])) {

            $query->where(
                'book_returns.book_id',
                $filters['book_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Condition
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['book_condition'])) {

            $query->where(
                'book_returns.book_condition',
                $filters['book_condition']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Fine Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['fine_status'])) {

            $query->where(
                'book_returns.fine_status',
                $filters['fine_status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Overdue
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['overdue']) &&
            $filters['overdue'] !== ''
        ) {

            if ((bool) $filters['overdue']) {

                $query->where(
                    'book_returns.overdue_days',
                    '>',
                    0
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['from_date'])) {

            $query->whereDate(
                'book_returns.return_date',
                '>=',
                $filters['from_date']
            );
        }


        if (!empty($filters['to_date'])) {

            $query->whereDate(
                'book_returns.return_date',
                '<=',
                $filters['to_date']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sort
        |--------------------------------------------------------------------------
        */

        $query->orderByDesc(
            'book_returns.id'
        );


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        return $query->paginate(
            (int) (
                $filters['per_page']
                ?? 20
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Find
    |--------------------------------------------------------------------------
    */

    public function find(int $id): BookReturn
    {
        return $this->query()
            ->with([
                'bookIssue',
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'returnedBy',
            ])
            ->findOrFail($id);
    }


    /*
    |--------------------------------------------------------------------------
    | Find Active Issue
    |--------------------------------------------------------------------------
    */

    public function findActiveIssue(
        int $issueId
    ): ?BookIssue {

        return BookIssue::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'id',
                $issueId
            )
            ->whereIn(
                'status',
                [
                    'Issued',
                    'Overdue',
                ]
            )
            ->with([
                'libraryMember',
                'book',
                'bookCopy',
            ])
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Check Existing Return
    |--------------------------------------------------------------------------
    */

    public function existingReturn(
        int $issueId
    ): ?BookReturn {

        return $this->query()
            ->where(
                'book_issue_id',
                $issueId
            )
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data
    ): BookReturn {

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Generate Return Number
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['return_no']
            )
        ) {

            $data['return_no'] =
                $this->generateReturnNo(
                    $schoolId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Return Number
        |--------------------------------------------------------------------------
        */

        $exists =
            BookReturn::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'return_no',
                    $data['return_no']
                )
                ->exists();


        if ($exists) {

            throw ValidationException::withMessages([
                'return_no' =>
                    'Return number already exists.',
            ]);
        }


        $data['school_id'] =
            $schoolId;

        $data['returned_by'] =
            auth()->id();


        return BookReturn::create(
            $data
        )->load([
            'bookIssue',
            'libraryMember.student',
            'libraryMember.staff',
            'book',
            'bookCopy',
            'returnedBy',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        array $data
    ): BookReturn {

        $return =
            $this->find($id);


        $return->update(
            $data
        );


        return $return->fresh([
            'bookIssue',
            'libraryMember.student',
            'libraryMember.staff',
            'book',
            'bookCopy',
            'returnedBy',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id
    ): bool {

        $return =
            $this->find($id);


        return (bool) $return->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Return Number
    |--------------------------------------------------------------------------
    */

    public function generateReturnNo(
        int $schoolId
    ): string {

        $prefix =
            'RET-' .
            now()->format('Ym') .
            '-';


        $last =
            BookReturn::withTrashed()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'return_no',
                    'like',
                    $prefix . '%'
                )
                ->orderByDesc(
                    'id'
                )
                ->value(
                    'return_no'
                );


        if (!$last) {

            return $prefix . '0001';
        }


        $number =
            (int) substr(
                $last,
                strlen($prefix)
            );


        return $prefix .
            str_pad(
                $number + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    public function statistics(): array
    {
        $query =
            $this->query();


        return [

            'total' =>
                (clone $query)->count(),

            'overdue' =>
                (clone $query)
                    ->where(
                        'overdue_days',
                        '>',
                        0
                    )
                    ->count(),

            'fine_pending' =>
                (clone $query)
                    ->where(
                        'fine_status',
                        'Pending'
                    )
                    ->count(),

            'fine_paid' =>
                (clone $query)
                    ->where(
                        'fine_status',
                        'Paid'
                    )
                    ->count(),

            'fine_amount' =>
                (float) (
                    (clone $query)
                        ->sum(
                            'fine_amount'
                        )
                ),

            'lost' =>
                (clone $query)
                    ->where(
                        'book_condition',
                        'Lost'
                    )
                    ->count(),

            'damaged' =>
                (clone $query)
                    ->where(
                        'book_condition',
                        'Damaged'
                    )
                    ->count(),

            'good' =>
                (clone $query)
                    ->where(
                        'book_condition',
                        'Good'
                    )
                    ->count(),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Pending Fines
    |--------------------------------------------------------------------------
    */

    public function pendingFines()
    {
        return $this->query()
            ->with([
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
            ])
            ->where(
                'fine_status',
                'Pending'
            )
            ->where(
                'fine_amount',
                '>',
                0
            )
            ->orderByDesc(
                'return_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Member Return History
    |--------------------------------------------------------------------------
    */

    public function memberReturns(
        int $memberId
    ) {
        return $this->query()
            ->with([
                'bookIssue',
                'book',
                'bookCopy',
            ])
            ->where(
                'library_member_id',
                $memberId
            )
            ->orderByDesc(
                'return_date'
            )
            ->get();
    }
}