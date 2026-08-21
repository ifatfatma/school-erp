<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookIssue;
use App\Models\BookReturn;
use App\Models\BookRenewal;
use App\Models\LibraryMember;
use App\Models\LibraryFine;
use App\Models\FineCollection;
use App\Models\LostDamagedBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LibraryReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $schoolId = $user->school_id;

        $books = Book::where(
            'school_id',
            $schoolId
        )->count();

        $copies = BookCopy::where(
            'school_id',
            $schoolId
        )->count();

        $members = LibraryMember::where(
            'school_id',
            $schoolId
        )->count();

        $issued = BookIssue::where(
            'school_id',
            $schoolId
        )
            ->where('status', 'Issued')
            ->count();

        $returned = BookIssue::where(
            'school_id',
            $schoolId
        )
            ->where('status', 'Returned')
            ->count();

        $overdue = BookIssue::where(
            'school_id',
            $schoolId
        )
            ->whereIn('status', [
                'Issued',
                'Overdue'
            ])
            ->whereDate(
                'due_date',
                '<',
                today()
            )
            ->count();

        $lost = LostDamagedBook::where(
            'school_id',
            $schoolId
        )
            ->where('type', 'Lost')
            ->count();

        $damaged = LostDamagedBook::where(
            'school_id',
            $schoolId
        )
            ->where('type', 'Damaged')
            ->count();

        $outstandingFine = LibraryFine::where(
            'school_id',
            $schoolId
        )
            ->whereIn('status', [
                'Pending',
                'Partially Paid'
            ])
            ->get()
            ->sum(function ($fine) {

                $paid = FineCollection::where(
                    'library_fine_id',
                    $fine->id
                )->sum('amount');

                return max(
                    0,
                    (float) $fine->payable_amount -
                        (float) $paid
                );
            });

        $fineCollected = FineCollection::where(
            'school_id',
            $schoolId
        )->sum('amount');

        $totalFines = LibraryFine::where(
            'school_id',
            $schoolId
        )->sum('payable_amount');

        $renewals = BookRenewal::where(
            'school_id',
            $schoolId
        )->count();

        return response()->json([
            'status' => 200,

            'data' => [
                'books' => $books,
                'copies' => $copies,
                'members' => $members,
                'issued' => $issued,
                'returned' => $returned,
                'overdue' => $overdue,
                'renewals' => $renewals,
                'lost' => $lost,
                'damaged' => $damaged,
                'total_fines' => (float) $totalFines,
                'outstanding_fine' => (float) $outstandingFine,
                'fine_collected' => (float) $fineCollected,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Issued Books
    |--------------------------------------------------------------------------
    */

    public function issuedBooks(Request $request)
    {
        $user = auth()->user();

        $query = BookIssue::with([
            'libraryMember',
            'book',
            'bookCopy',
        ])
            ->where(
                'school_id',
                $user->school_id
            )
            ->where('status', 'Issued');

        $this->applyIssueFilters(
            $query,
            $request
        );

        $items = $query
            ->orderByDesc('id')
            ->paginate(
                $request->get('per_page', 20)
            );

        return $this->paginateResponse($items);
    }


    /*
    |--------------------------------------------------------------------------
    | Returned Books
    |--------------------------------------------------------------------------
    */

    public function returnedBooks(Request $request)
    {
        $user = auth()->user();

        $query = BookIssue::with([
            'libraryMember',
            'book',
            'bookCopy',
        ])
            ->where(
                'school_id',
                $user->school_id
            )
            ->where('status', 'Returned');

        $this->applyIssueFilters(
            $query,
            $request
        );

        $items = $query
            ->orderByDesc('id')
            ->paginate(
                $request->get('per_page', 20)
            );

        return $this->paginateResponse($items);
    }


    /*
    |--------------------------------------------------------------------------
    | Overdue Books
    |--------------------------------------------------------------------------
    */

    public function overdueBooks(Request $request)
    {
        $user = auth()->user();

        $query = BookIssue::with([
            'libraryMember',
            'book',
            'bookCopy',
        ])
            ->where(
                'school_id',
                $user->school_id
            )
            ->whereIn('status', [
                'Issued',
                'Overdue'
            ])
            ->whereDate(
                'due_date',
                '<',
                today()
            );

        $this->applyIssueFilters(
            $query,
            $request
        );

        $items = $query
            ->orderBy('due_date')
            ->paginate(
                $request->get('per_page', 20)
            );

        return $this->paginateResponse($items);
    }


    /*
    |--------------------------------------------------------------------------
    | Renewals
    |--------------------------------------------------------------------------
    */

    public function renewals(Request $request)
    {
        $user = auth()->user();

        $query = BookRenewal::with([
            'bookIssue.libraryMember',
            'bookIssue.book',
            'bookIssue.bookCopy',
        ])
            ->where(
                'school_id',
                $user->school_id
            );

        if ($request->filled('from_date')) {
            $query->whereDate(
                'renewal_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'renewal_date',
                '<=',
                $request->to_date
            );
        }

        $items = $query
            ->orderByDesc('id')
            ->paginate(
                $request->get('per_page', 20)
            );

        return $this->paginateResponse($items);
    }


    /*
    |--------------------------------------------------------------------------
    | Lost / Damaged
    |--------------------------------------------------------------------------
    */

    public function lostDamaged(Request $request)
    {
        $user = auth()->user();

        $query = LostDamagedBook::with([
            'bookIssue',
            'bookCopy',
            'libraryMember',
            'reportedBy',
        ])
            ->where(
                'school_id',
                $user->school_id
            );

        if ($request->filled('type')) {
            $query->where(
                'type',
                $request->type
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'reported_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'reported_date',
                '<=',
                $request->to_date
            );
        }

        $items = $query
            ->orderByDesc('id')
            ->paginate(
                $request->get('per_page', 20)
            );

        return $this->paginateResponse($items);
    }


    /*
    |--------------------------------------------------------------------------
    | Outstanding Fines
    |--------------------------------------------------------------------------
    */

    public function outstandingFines(Request $request)
    {
        $user = auth()->user();

        $query = LibraryFine::with([
            'bookIssue.libraryMember',
            'bookIssue.book',
            'bookIssue.bookCopy',
            'libraryMember',
            'collections',
        ])
            ->where(
                'school_id',
                $user->school_id
            )
            ->whereIn('status', [
                'Pending',
                'Partially Paid',
            ])
            ->where(
                'due_amount',
                '>',
                0
            );

        if ($request->filled('fine_type')) {
            $query->where(
                'fine_type',
                $request->fine_type
            );
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'fine_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'fine_date',
                '<=',
                $request->to_date
            );
        }

        $items = $query
            ->orderByDesc('id')
            ->paginate(
                $request->get('per_page', 20)
            );

        return $this->paginateResponse($items);
    }


    /*
    |--------------------------------------------------------------------------
    | Fine Collection
    |--------------------------------------------------------------------------
    */

    public function fineCollection(Request $request)
    {
        $user = auth()->user();

        $query = FineCollection::with([
            'libraryFine',
            'libraryMember',
            'collector',
        ])
            ->where(
                'school_id',
                $user->school_id
            );

        if ($request->filled('from_date')) {
            $query->whereDate(
                'payment_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'payment_date',
                '<=',
                $request->to_date
            );
        }

        if ($request->filled('payment_method')) {
            $query->where(
                'payment_method',
                $request->payment_method
            );
        }

        $items = $query
            ->orderByDesc('id')
            ->paginate(
                $request->get('per_page', 20)
            );

        return $this->paginateResponse($items);
    }


    /*
    |--------------------------------------------------------------------------
    | Member History
    |--------------------------------------------------------------------------
    */

    public function memberHistory(
        Request $request,
        $memberId
    ) {
        $user = auth()->user();

        $member = LibraryMember::where(
            'school_id',
            $user->school_id
        )
            ->with([
                'student',
                'staff',
            ])
            ->findOrFail($memberId);

        $issues = BookIssue::with([
            'book',
            'bookCopy',
            'renewals',
        ])
            ->where(
                'school_id',
                $user->school_id
            )
            ->where(
                'library_member_id',
                $member->id
            )
            ->orderByDesc('id')
            ->paginate(
                $request->get('per_page', 20)
            );

        $fines = LibraryFine::with([
            'collections',
        ])
            ->where(
                'school_id',
                $user->school_id
            )
            ->where(
                'library_member_id',
                $member->id
            )
            ->orderByDesc('id')
            ->get();

        $totalFine = $fines->sum(
            fn($fine) => (float) $fine->amount
        );

        $paidFine = $fines->sum(
            fn($fine) => (float) $fine->paid_amount
        );

        $dueFine = $fines->sum(
            fn($fine) => (float) $fine->due_amount
        );

        return response()->json([
            'status' => 200,

            'data' => [
                'member' => $member,

                'summary' => [
                    'total_issues' => $issues->total(),
                    'total_fine' => $totalFine,
                    'paid_fine' => $paidFine,
                    'due_fine' => $dueFine,
                ],

                'issues' => $issues,

                'fines' => $fines,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Apply Issue Filters
    |--------------------------------------------------------------------------
    */

    private function applyIssueFilters(
        $query,
        Request $request
    ) {
        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->where(
                    'issue_no',
                    'like',
                    "%{$search}%"
                )

                    ->orWhereHas(
                        'libraryMember',
                        function ($q) use ($search) {

                            $q->where(
                                'member_no',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )

                    ->orWhereHas(
                        'book',
                        function ($q) use ($search) {

                            $q->where(
                                'title',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )

                    ->orWhereHas(
                        'bookCopy',
                        function ($q) use ($search) {

                            $q->where(
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

        if ($request->filled('member_id')) {

            $query->where(
                'library_member_id',
                $request->member_id
            );
        }

        if ($request->filled('book_id')) {

            $query->where(
                'book_id',
                $request->book_id
            );
        }

        if ($request->filled('from_date')) {

            $query->whereDate(
                'issue_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {

            $query->whereDate(
                'issue_date',
                '<=',
                $request->to_date
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Pagination Response
    |--------------------------------------------------------------------------
    */

    private function paginateResponse($items)
    {
        return response()->json([
            'status' => 200,

            'data' => $items->items(),

            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
        ]);
    }
}
