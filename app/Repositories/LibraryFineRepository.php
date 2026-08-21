<?php

namespace App\Repositories;

use App\Models\LibraryFine;
use Illuminate\Validation\ValidationException;

class LibraryFineRepository
{
    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    protected function query()
    {
        return LibraryFine::query()
            ->where(
                'library_fines.school_id',
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
                'bookIssue',
                'book',
                'bookCopy',
                'lostDamagedBook',
                'createdBy',
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
                    'library_fines.fine_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'libraryMember',
                    function ($member) use ($search) {

                        $member
                            ->where(
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

                        $book
                            ->where(
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
                    'bookCopy',
                    function ($copy) use ($search) {

                        $copy
                            ->where(
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
        | Fine Type
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['fine_type'])) {

            $query->where(
                'library_fines.fine_type',
                $filters['fine_type']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['status'])) {

            $query->where(
                'library_fines.status',
                $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Member
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['library_member_id'])) {

            $query->where(
                'library_fines.library_member_id',
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
                'library_fines.book_id',
                $filters['book_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Book Issue
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['book_issue_id'])) {

            $query->where(
                'library_fines.book_issue_id',
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
                'library_fines.fine_date',
                '>=',
                $filters['from_date']
            );
        }


        if (!empty($filters['to_date'])) {

            $query->whereDate(
                'library_fines.fine_date',
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
            'library_fines.id'
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

    public function find(
        int $id
    ): LibraryFine {

        return $this->query()
            ->with([
                'libraryMember.student',
                'libraryMember.staff',
                'bookIssue',
                'book',
                'bookCopy',
                'lostDamagedBook',
                'createdBy',
                'collections',
            ])
            ->findOrFail($id);
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data
    ): LibraryFine {

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Generate Fine Number
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['fine_no']
            )
        ) {

            $data['fine_no'] =
                $this->generateFineNo(
                    $schoolId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Fine Number
        |--------------------------------------------------------------------------
        */

        $exists =
            LibraryFine::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'fine_no',
                    $data['fine_no']
                )
                ->exists();


        if ($exists) {

            throw ValidationException::withMessages([
                'fine_no' =>
                    'Fine number already exists.',
            ]);
        }


        $data['school_id'] =
            $schoolId;

        $data['created_by'] =
            auth()->id();


        return LibraryFine::create(
            $data
        )->load([
            'libraryMember.student',
            'libraryMember.staff',
            'bookIssue',
            'book',
            'bookCopy',
            'lostDamagedBook',
            'createdBy',
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
    ): LibraryFine {

        $fine =
            $this->find($id);


        $fine->update(
            $data
        );


        return $fine->fresh([
            'libraryMember.student',
            'libraryMember.staff',
            'bookIssue',
            'book',
            'bookCopy',
            'lostDamagedBook',
            'createdBy',
            'collections',
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

        $fine =
            $this->find($id);


        /*
        |--------------------------------------------------------------------------
        | Do not delete a fine with collections
        |--------------------------------------------------------------------------
        */

        if (
            $fine->collections()->exists()
        ) {

            throw ValidationException::withMessages([
                'fine' =>
                    'A fine with collection/payment records cannot be deleted.',
            ]);
        }


        return (bool) $fine->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Fine Number
    |--------------------------------------------------------------------------
    */

    public function generateFineNo(
        int $schoolId
    ): string {

        $prefix =
            'FINE-' .
            now()->format('Ym') .
            '-';


        $last =
            LibraryFine::withTrashed()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'fine_no',
                    'like',
                    $prefix . '%'
                )
                ->orderByDesc(
                    'id'
                )
                ->value(
                    'fine_no'
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

            'pending' =>
                (clone $query)
                    ->where(
                        'status',
                        'Pending'
                    )
                    ->count(),

            'partially_paid' =>
                (clone $query)
                    ->where(
                        'status',
                        'Partially Paid'
                    )
                    ->count(),

            'paid' =>
                (clone $query)
                    ->where(
                        'status',
                        'Paid'
                    )
                    ->count(),

            'waived' =>
                (clone $query)
                    ->where(
                        'status',
                        'Waived'
                    )
                    ->count(),

            'total_fine' =>
                (float) (
                    (clone $query)
                        ->sum(
                            'fine_amount'
                        )
                ),

            'total_waiver' =>
                (float) (
                    (clone $query)
                        ->sum(
                            'waiver_amount'
                        )
                ),

            'total_payable' =>
                (float) (
                    (clone $query)
                        ->sum(
                            'payable_amount'
                        )
                ),

            'pending_amount' =>
                (float) (
                    (clone $query)
                        ->whereIn(
                            'status',
                            [
                                'Pending',
                                'Partially Paid',
                            ]
                        )
                        ->sum(
                            'payable_amount'
                        )
                ),

            'collected_amount' =>
                (float) (
                    (clone $query)
                        ->withSum(
                            'collections',
                            'amount'
                        )
                        ->get()
                        ->sum(
                            'collections_sum_amount'
                        )
                ),

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
                'bookIssue',
            ])
            ->whereIn(
                'status',
                [
                    'Pending',
                    'Partially Paid',
                ]
            )
            ->orderByDesc(
                'fine_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Member Fines
    |--------------------------------------------------------------------------
    */

    public function memberFines(
        int $memberId
    ) {
        return $this->query()
            ->with([
                'bookIssue',
                'book',
                'bookCopy',
                'collections',
            ])
            ->where(
                'library_member_id',
                $memberId
            )
            ->orderByDesc(
                'fine_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Issue Fines
    |--------------------------------------------------------------------------
    */

    public function issueFines(
        int $issueId
    ) {
        return $this->query()
            ->with([
                'libraryMember',
                'book',
                'bookCopy',
                'collections',
            ])
            ->where(
                'book_issue_id',
                $issueId
            )
            ->orderByDesc(
                'fine_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Overdue Fines
    |--------------------------------------------------------------------------
    */

    public function overdueFines()
    {
        return $this->query()
            ->with([
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'bookIssue',
            ])
            ->where(
                'fine_type',
                'Overdue'
            )
            ->orderByDesc(
                'fine_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Lost / Damaged Fines
    |--------------------------------------------------------------------------
    */

    public function lostDamagedFines()
    {
        return $this->query()
            ->with([
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'lostDamagedBook',
            ])
            ->whereIn(
                'fine_type',
                [
                    'Lost',
                    'Damaged',
                ]
            )
            ->orderByDesc(
                'fine_date'
            )
            ->get();
    }
}