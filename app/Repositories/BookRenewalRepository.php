<?php

namespace App\Repositories;

use App\Models\BookIssue;
use App\Models\BookRenewal;
use Illuminate\Validation\ValidationException;

class BookRenewalRepository
{
    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    protected function query()
    {
        return BookRenewal::query()
            ->where(
                'book_renewals.school_id',
                auth()->user()->school_id
            );
    }


    /*
    |--------------------------------------------------------------------------
    | List
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
                'renewedBy',
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
                    'book_renewals.renewal_no',
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
                'book_renewals.library_member_id',
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
                'book_renewals.book_id',
                $filters['book_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Issue
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['book_issue_id'])) {

            $query->where(
                'book_renewals.book_issue_id',
                $filters['book_issue_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['from_date'])) {

            $query->whereDate(
                'book_renewals.renewal_date',
                '>=',
                $filters['from_date']
            );
        }


        if (!empty($filters['to_date'])) {

            $query->whereDate(
                'book_renewals.renewal_date',
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
            'book_renewals.id'
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

    public function find(int $id): BookRenewal
    {
        return $this->query()
            ->with([
                'bookIssue',
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'renewedBy',
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
            ->lockForUpdate()
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Renewal Count
    |--------------------------------------------------------------------------
    */

    public function renewalCount(
        int $issueId
    ): int {

        return $this->query()
            ->where(
                'book_issue_id',
                $issueId
            )
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | Last Renewal
    |--------------------------------------------------------------------------
    */

    public function lastRenewal(
        int $issueId
    ): ?BookRenewal {

        return $this->query()
            ->where(
                'book_issue_id',
                $issueId
            )
            ->orderByDesc(
                'renewal_count'
            )
            ->orderByDesc(
                'id'
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
    ): BookRenewal {

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Generate Renewal Number
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['renewal_no']
            )
        ) {

            $data['renewal_no'] =
                $this->generateRenewalNo(
                    $schoolId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Duplicate
        |--------------------------------------------------------------------------
        */

        $exists =
            BookRenewal::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'renewal_no',
                    $data['renewal_no']
                )
                ->exists();


        if ($exists) {

            throw ValidationException::withMessages([
                'renewal_no' =>
                    'Renewal number already exists.',
            ]);
        }


        $data['school_id'] =
            $schoolId;

        $data['renewed_by'] =
            auth()->id();


        return BookRenewal::create(
            $data
        )->load([
            'bookIssue',
            'libraryMember.student',
            'libraryMember.staff',
            'book',
            'bookCopy',
            'renewedBy',
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
    ): BookRenewal {

        $renewal =
            $this->find($id);


        $renewal->update(
            $data
        );


        return $renewal->fresh([
            'bookIssue',
            'libraryMember.student',
            'libraryMember.staff',
            'book',
            'bookCopy',
            'renewedBy',
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

        $renewal =
            $this->find($id);

        return (bool) $renewal->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Renewal Number
    |--------------------------------------------------------------------------
    */

    public function generateRenewalNo(
        int $schoolId
    ): string {

        $prefix =
            'REN-' .
            now()->format('Ym') .
            '-';


        $last =
            BookRenewal::withTrashed()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'renewal_no',
                    'like',
                    $prefix . '%'
                )
                ->orderByDesc(
                    'id'
                )
                ->value(
                    'renewal_no'
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

            'today' =>
                (clone $query)
                    ->whereDate(
                        'renewal_date',
                        now()->toDateString()
                    )
                    ->count(),

            'this_month' =>
                (clone $query)
                    ->whereYear(
                        'renewal_date',
                        now()->year
                    )
                    ->whereMonth(
                        'renewal_date',
                        now()->month
                    )
                    ->count(),

            'members' =>
                (clone $query)
                    ->distinct(
                        'library_member_id'
                    )
                    ->count(
                        'library_member_id'
                    ),

            'books' =>
                (clone $query)
                    ->distinct(
                        'book_id'
                    )
                    ->count(
                        'book_id'
                    ),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Member Renewal History
    |--------------------------------------------------------------------------
    */

    public function memberRenewals(
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
                'renewal_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Issue Renewal History
    |--------------------------------------------------------------------------
    */

    public function issueRenewals(
        int $issueId
    ) {
        return $this->query()
            ->with([
                'libraryMember',
                'book',
                'bookCopy',
            ])
            ->where(
                'book_issue_id',
                $issueId
            )
            ->orderBy(
                'renewal_count'
            )
            ->get();
    }
}