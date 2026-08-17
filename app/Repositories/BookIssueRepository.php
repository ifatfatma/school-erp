<?php

namespace App\Repositories;

use App\Models\BookIssue;
use Illuminate\Validation\ValidationException;

class BookIssueRepository
{
    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    protected function query()
    {
        return BookIssue::query()
            ->where(
                'book_issues.school_id',
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
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'issuedBy',
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
                    'book_issues.issue_no',
                    'like',
                    "%{$search}%"
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
                'book_issues.library_member_id',
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
                'book_issues.book_id',
                $filters['book_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Book Copy
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['book_copy_id'])) {

            $query->where(
                'book_issues.book_copy_id',
                $filters['book_copy_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['status'])) {

            $query->where(
                'book_issues.status',
                $filters['status']
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

            if (
                (bool) $filters['overdue']
            ) {

                $query->whereIn(
                    'book_issues.status',
                    [
                        'Issued',
                        'Overdue',
                    ]
                )
                ->whereDate(
                    'book_issues.due_date',
                    '<',
                    now()->toDateString()
                );

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['from_date'])) {

            $query->whereDate(
                'book_issues.issue_date',
                '>=',
                $filters['from_date']
            );
        }


        if (!empty($filters['to_date'])) {

            $query->whereDate(
                'book_issues.issue_date',
                '<=',
                $filters['to_date']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'book_issues.id',
            'desc'
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

    public function find(int $id): BookIssue
    {
        return $this->query()
            ->with([
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'issuedBy',
                'returnedBy',
            ])
            ->findOrFail($id);
    }


    /*
    |--------------------------------------------------------------------------
    | Active Issues
    |--------------------------------------------------------------------------
    */

    public function activeForMember(
        int $memberId
    ) {

        return $this->query()
            ->where(
                'library_member_id',
                $memberId
            )
            ->whereIn(
                'status',
                [
                    'Issued',
                    'Overdue',
                ]
            )
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | Existing Active Copy Issue
    |--------------------------------------------------------------------------
    */

    public function activeForCopy(
        int $copyId
    ): ?BookIssue {

        return $this->query()
            ->where(
                'book_copy_id',
                $copyId
            )
            ->whereIn(
                'status',
                [
                    'Issued',
                    'Overdue',
                ]
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
    ): BookIssue {

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Issue Number
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['issue_no']
            )
        ) {

            $data['issue_no'] =
                $this->generateIssueNo(
                    $schoolId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Issue Number
        |--------------------------------------------------------------------------
        */

        $exists =
            BookIssue::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'issue_no',
                    $data['issue_no']
                )
                ->exists();


        if ($exists) {

            throw ValidationException::withMessages([
                'issue_no' =>
                    'Issue number already exists.',
            ]);
        }


        $data['school_id'] =
            $schoolId;

        $data['issued_by'] =
            auth()->id();


        return BookIssue::create(
            $data
        )->load([
            'libraryMember.student',
            'libraryMember.staff',
            'book',
            'bookCopy',
            'issuedBy',
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
    ): BookIssue {

        $issue =
            $this->find($id);


        /*
        |--------------------------------------------------------------------------
        | Do not modify returned issue
        |--------------------------------------------------------------------------
        */

        if (
            $issue->status === 'Returned'
        ) {

            throw ValidationException::withMessages([
                'issue' =>
                    'A returned book issue cannot be modified.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Issue Number
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['issue_no']
            )
        ) {

            $exists =
                BookIssue::query()
                    ->where(
                        'school_id',
                        auth()->user()->school_id
                    )
                    ->where(
                        'issue_no',
                        $data['issue_no']
                    )
                    ->where(
                        'id',
                        '!=',
                        $issue->id
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'issue_no' =>
                        'Issue number already exists.',
                ]);
            }
        }


        $issue->update(
            $data
        );


        return $issue->fresh([
            'libraryMember.student',
            'libraryMember.staff',
            'book',
            'bookCopy',
            'issuedBy',
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

        $issue =
            $this->find($id);


        if (
            in_array(
                $issue->status,
                [
                    'Issued',
                    'Overdue',
                ],
                true
            )
        ) {

            throw ValidationException::withMessages([
                'issue' =>
                    'An active book issue cannot be deleted. Return the book first.',
            ]);
        }


        return (bool) $issue->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Issue Number
    |--------------------------------------------------------------------------
    */

    public function generateIssueNo(
        int $schoolId
    ): string {

        $prefix =
            'ISS-' .
            now()->format('Ym') .
            '-';


        $last =
            BookIssue::withTrashed()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'issue_no',
                    'like',
                    $prefix . '%'
                )
                ->orderByDesc(
                    'id'
                )
                ->value(
                    'issue_no'
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

            'issued' =>
                (clone $query)
                    ->where(
                        'status',
                        'Issued'
                    )
                    ->count(),

            'returned' =>
                (clone $query)
                    ->where(
                        'status',
                        'Returned'
                    )
                    ->count(),

            'overdue' =>
                (clone $query)
                    ->whereIn(
                        'status',
                        [
                            'Issued',
                            'Overdue',
                        ]
                    )
                    ->whereDate(
                        'due_date',
                        '<',
                        now()->toDateString()
                    )
                    ->count(),

            'lost' =>
                (clone $query)
                    ->where(
                        'status',
                        'Lost'
                    )
                    ->count(),

            'damaged' =>
                (clone $query)
                    ->where(
                        'status',
                        'Damaged'
                    )
                    ->count(),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Overdue Issues
    |--------------------------------------------------------------------------
    */

    public function overdue()
    {
        return $this->query()
            ->with([
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
            ])
            ->whereIn(
                'book_issues.status',
                [
                    'Issued',
                    'Overdue',
                ]
            )
            ->whereDate(
                'book_issues.due_date',
                '<',
                now()->toDateString()
            )
            ->orderBy(
                'book_issues.due_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Member Issues
    |--------------------------------------------------------------------------
    */

    public function memberIssues(
        int $memberId
    ) {
        return $this->query()
            ->with([
                'book',
                'bookCopy',
            ])
            ->where(
                'library_member_id',
                $memberId
            )
            ->orderByDesc(
                'issue_date'
            )
            ->get();
    }
}