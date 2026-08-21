<?php

namespace App\Repositories;

use App\Models\LostDamagedBook;
use Illuminate\Validation\ValidationException;

class LostDamagedBookRepository
{
    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    protected function query()
    {
        return LostDamagedBook::query()
            ->where(
                'lost_damaged_books.school_id',
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
                'reportedBy',
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
                    'lost_damaged_books.report_no',
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
        | Type
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['type'])) {

            $query->where(
                'lost_damaged_books.type',
                $filters['type']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Member
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['library_member_id'])) {

            $query->where(
                'lost_damaged_books.library_member_id',
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
                'lost_damaged_books.book_id',
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
                'lost_damaged_books.book_copy_id',
                $filters['book_copy_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Charge Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['charge_status'])) {

            $query->where(
                'lost_damaged_books.charge_status',
                $filters['charge_status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Resolution
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['resolution'])) {

            $query->where(
                'lost_damaged_books.resolution',
                $filters['resolution']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['from_date'])) {

            $query->whereDate(
                'lost_damaged_books.report_date',
                '>=',
                $filters['from_date']
            );
        }


        if (!empty($filters['to_date'])) {

            $query->whereDate(
                'lost_damaged_books.report_date',
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
            'lost_damaged_books.id'
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
    ): LostDamagedBook {

        return $this->query()
            ->with([
                'bookIssue',
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'reportedBy',
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
    ): LostDamagedBook {

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Generate Report Number
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['report_no']
            )
        ) {

            $data['report_no'] =
                $this->generateReportNo(
                    $schoolId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Report Number
        |--------------------------------------------------------------------------
        */

        $exists =
            LostDamagedBook::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'report_no',
                    $data['report_no']
                )
                ->exists();


        if ($exists) {

            throw ValidationException::withMessages([
                'report_no' =>
                    'Report number already exists.',
            ]);
        }


        $data['school_id'] =
            $schoolId;

        $data['reported_by'] =
            auth()->id();


        return LostDamagedBook::create(
            $data
        )->load([
            'bookIssue',
            'libraryMember.student',
            'libraryMember.staff',
            'book',
            'bookCopy',
            'reportedBy',
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
    ): LostDamagedBook {

        $record =
            $this->find($id);


        $record->update(
            $data
        );


        return $record->fresh([
            'bookIssue',
            'libraryMember.student',
            'libraryMember.staff',
            'book',
            'bookCopy',
            'reportedBy',
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

        $record =
            $this->find($id);

        return (bool) $record->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Report Number
    |--------------------------------------------------------------------------
    */

    public function generateReportNo(
        int $schoolId
    ): string {

        $prefix =
            'LD-' .
            now()->format('Ym') .
            '-';


        $last =
            LostDamagedBook::withTrashed()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'report_no',
                    'like',
                    $prefix . '%'
                )
                ->orderByDesc(
                    'id'
                )
                ->value(
                    'report_no'
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

            'lost' =>
                (clone $query)
                    ->where(
                        'type',
                        'Lost'
                    )
                    ->count(),

            'damaged' =>
                (clone $query)
                    ->where(
                        'type',
                        'Damaged'
                    )
                    ->count(),

            'pending_charges' =>
                (clone $query)
                    ->where(
                        'charge_status',
                        'Pending'
                    )
                    ->where(
                        'charge_amount',
                        '>',
                        0
                    )
                    ->count(),

            'paid_charges' =>
                (clone $query)
                    ->where(
                        'charge_status',
                        'Paid'
                    )
                    ->count(),

            'waived_charges' =>
                (clone $query)
                    ->where(
                        'charge_status',
                        'Waived'
                    )
                    ->count(),

            'pending_amount' =>
                (float) (
                    (clone $query)
                        ->where(
                            'charge_status',
                            'Pending'
                        )
                        ->sum(
                            'charge_amount'
                        )
                ),

            'paid_amount' =>
                (float) (
                    (clone $query)
                        ->where(
                            'charge_status',
                            'Paid'
                        )
                        ->sum(
                            'charge_amount'
                        )
                ),

            'total_charge' =>
                (float) (
                    (clone $query)
                        ->sum(
                            'charge_amount'
                        )
                ),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Pending Charges
    |--------------------------------------------------------------------------
    */

    public function pendingCharges()
    {
        return $this->query()
            ->with([
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
            ])
            ->where(
                'charge_status',
                'Pending'
            )
            ->where(
                'charge_amount',
                '>',
                0
            )
            ->orderByDesc(
                'report_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Lost Books
    |--------------------------------------------------------------------------
    */

    public function lostBooks()
    {
        return $this->query()
            ->with([
                'bookIssue',
                'libraryMember',
                'book',
                'bookCopy',
            ])
            ->where(
                'type',
                'Lost'
            )
            ->orderByDesc(
                'report_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Damaged Books
    |--------------------------------------------------------------------------
    */

    public function damagedBooks()
    {
        return $this->query()
            ->with([
                'bookIssue',
                'libraryMember',
                'book',
                'bookCopy',
            ])
            ->where(
                'type',
                'Damaged'
            )
            ->orderByDesc(
                'report_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Member History
    |--------------------------------------------------------------------------
    */

    public function memberHistory(
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
                'report_date'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Book Copy History
    |--------------------------------------------------------------------------
    */

    public function copyHistory(
        int $copyId
    ) {
        return $this->query()
            ->with([
                'bookIssue',
                'libraryMember',
                'book',
            ])
            ->where(
                'book_copy_id',
                $copyId
            )
            ->orderByDesc(
                'report_date'
            )
            ->get();
    }
}