<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LibraryMember;
use App\Models\BookIssue;
use App\Models\BookReturn;
use App\Models\BookRenewal;
use App\Models\LostDamagedBook;
use App\Models\LibraryFine;
use App\Models\FineCollection;
use Illuminate\Http\Request;

class LibraryReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | School ID
    |--------------------------------------------------------------------------
    */

    protected function schoolId()
    {
        return auth()->user()->school_id;
    }


    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard(Request $request)
    {
        $schoolId = $this->schoolId();

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
        ->where(
            'status',
            'Issued'
        )
        ->count();

        $returned = BookReturn::where(
            'school_id',
            $schoolId
        )->count();

        $overdue = BookIssue::where(
            'school_id',
            $schoolId
        )
        ->where(
            'status',
            'Issued'
        )
        ->whereDate(
            'due_date',
            '<',
            now()->toDateString()
        )
        ->count();

        $renewals = BookRenewal::where(
            'school_id',
            $schoolId
        )->count();

        $lost = LostDamagedBook::where(
            'school_id',
            $schoolId
        )
        ->where(
            'type',
            'Lost'
        )
        ->count();

        $damaged = LostDamagedBook::where(
            'school_id',
            $schoolId
        )
        ->where(
            'type',
            'Damaged'
        )
        ->count();

        $totalFines = LibraryFine::where(
            'school_id',
            $schoolId
        )->sum('payable_amount');

        $fineCollected = FineCollection::where(
            'school_id',
            $schoolId
        )->sum('amount');

        $outstandingFine = LibraryFine::where(
            'school_id',
            $schoolId
        )
        ->whereIn(
            'status',
            [
                'Pending',
                'Partially Paid'
            ]
        )
        ->get()
        ->sum(function ($fine) use ($schoolId) {

            $paid = FineCollection::where(
                'school_id',
                $schoolId
            )
            ->where(
                'library_fine_id',
                $fine->id
            )
            ->sum('amount');

            return max(
                0,
                (float) $fine->payable_amount -
                (float) $paid
            );
        });

        return response()->json([
            'success' => true,

            'data' => [
                'books' => (int) $books,
                'copies' => (int) $copies,
                'members' => (int) $members,
                'issued' => (int) $issued,
                'returned' => (int) $returned,
                'overdue' => (int) $overdue,
                'renewals' => (int) $renewals,
                'lost' => (int) $lost,
                'damaged' => (int) $damaged,
                'total_fines' => (float) $totalFines,
                'outstanding_fine' => (float) $outstandingFine,
                'fine_collected' => (float) $fineCollected,
            ]
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Issued Books
    |--------------------------------------------------------------------------
    */

   public function issuedBooks(Request $request)
{
    $schoolId = auth()->user()->school_id;

    $query = BookIssue::query()
        ->where('school_id', $schoolId);

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */
    if ($request->filled('search')) {

        $search = trim($request->search);

        $query->where(function ($q) use ($search) {

            $q->where('issue_no', 'like', "%{$search}%")

                ->orWhereHas('book', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })

                ->orWhereHas('libraryMember', function ($q) use ($search) {
                    $q->where('member_no', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */
    if ($request->filled('status')) {
        $query->where(
            'status',
            $request->status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Date Filter
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Overdue
    |--------------------------------------------------------------------------
    */
    if (
        $request->filled('overdue') &&
        $request->overdue == '1'
    ) {
        $query
            ->where('status', 'Issued')
            ->whereDate(
                'due_date',
                '<',
                now()->toDateString()
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Get Records
    |--------------------------------------------------------------------------
    */
    $results = $query
        ->latest('issue_date')
        ->paginate(
            $request->integer('per_page', 20)
        );

    /*
    |--------------------------------------------------------------------------
    | Attach Correct Names Expected By Vue
    |--------------------------------------------------------------------------
    */
    $results->getCollection()->transform(
        function ($issue) use ($schoolId) {

            /*
            |--------------------------------------------------------------
            | Member
            |--------------------------------------------------------------
            */

            $issue->library_member =
                LibraryMember::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $issue->library_member_id
                )
                ->first();


            /*
            |--------------------------------------------------------------
            | Book
            |--------------------------------------------------------------
            */

            $issue->book =
                Book::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $issue->book_id
                )
                ->first();


            /*
            |--------------------------------------------------------------
            | Book Copy
            |--------------------------------------------------------------
            */

            $issue->book_copy =
                BookCopy::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $issue->book_copy_id
                )
                ->first();


            return $issue;
        }
    );

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | Returned Books
    |--------------------------------------------------------------------------
    */

    public function returnedBooks(Request $request)
{
    $schoolId = auth()->user()->school_id;

    $query = BookIssue::query()
        ->where('school_id', $schoolId)
        ->where('status', 'Returned');

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */
    if ($request->filled('search')) {

        $search = trim($request->search);

        $query->where(function ($q) use ($search) {

            $q->where('issue_no', 'like', "%{$search}%")

                ->orWhereHas('book', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })

                ->orWhereHas('libraryMember', function ($q) use ($search) {
                    $q->where('member_no', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Date Filter
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Load Relationships
    |--------------------------------------------------------------------------
    */
    $results = $query
        ->with([
            'libraryMember',
            'book',
            'bookCopy'
        ])
        ->latest('issue_date')
        ->paginate(
            $request->integer('per_page', 20)
        );

    /*
    |--------------------------------------------------------------------------
    | Normalize Relationship Names For Vue
    |--------------------------------------------------------------------------
    */
    $results->getCollection()->transform(function ($issue) {

        // Vue expects library_member
        $issue->setAttribute(
            'library_member',
            $issue->libraryMember
        );

        // Vue expects book
        $issue->setAttribute(
            'book',
            $issue->book
        );

        // Vue expects book_copy
        $issue->setAttribute(
            'book_copy',
            $issue->bookCopy
        );

        return $issue;
    });

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | Overdue Books
    |--------------------------------------------------------------------------
    */

    public function overdueBooks(Request $request)
{
    $schoolId = $this->schoolId();

    $query = BookIssue::query()
        ->where('school_id', $schoolId)
        ->where('status', 'Issued')
        ->whereDate(
            'due_date',
            '<',
            now()->toDateString()
        );

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = trim($request->search);

        $query->where(function ($q) use ($search) {

            $q->where(
                'issue_no',
                'like',
                "%{$search}%"
            );

            $q->orWhereHas(
                'book',
                function ($q) use ($search) {

                    $q->where(
                        'title',
                        'like',
                        "%{$search}%"
                    );
                }
            );

            $q->orWhereHas(
                'libraryMember',
                function ($q) use ($search) {

                    $q->where(
                        'member_no',
                        'like',
                        "%{$search}%"
                    );

                    $q->orWhere(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | From Date
    |--------------------------------------------------------------------------
    */

    if ($request->filled('from_date')) {

        $query->whereDate(
            'due_date',
            '>=',
            $request->from_date
        );
    }

    /*
    |--------------------------------------------------------------------------
    | To Date
    |--------------------------------------------------------------------------
    */

    if ($request->filled('to_date')) {

        $query->whereDate(
            'due_date',
            '<=',
            $request->to_date
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $results = $query
        ->latest('due_date')
        ->paginate(
            $request->integer(
                'per_page',
                20
            )
        );

    /*
    |--------------------------------------------------------------------------
    | Add Member / Book / Copy
    |--------------------------------------------------------------------------
    */

    $results->getCollection()
        ->transform(function ($issue) use ($schoolId) {

            /*
            |--------------------------------------------------------------------------
            | Member
            |--------------------------------------------------------------------------
            */

            $issue->library_member = null;

            if (!empty($issue->library_member_id)) {

                $issue->library_member =
                    LibraryMember::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $issue->library_member_id
                    )
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | Book
            |--------------------------------------------------------------------------
            */

            $issue->book = null;

            if (!empty($issue->book_id)) {

                $issue->book =
                    Book::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $issue->book_id
                    )
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | Book Copy
            |--------------------------------------------------------------------------
            */

            $issue->book_copy = null;

            if (!empty($issue->book_copy_id)) {

                $issue->book_copy =
                    BookCopy::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $issue->book_copy_id
                    )
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | Backward Compatible Names
            |--------------------------------------------------------------------------
            */

            $issue->member =
                $issue->library_member;

            $issue->copy =
                $issue->book_copy;

            return $issue;
        });

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | Renewals
    |--------------------------------------------------------------------------
    */

    public function renewals(Request $request)
{
    $schoolId = $this->schoolId();

    $query = BookRenewal::query()
        ->where(
            'school_id',
            $schoolId
        );

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = trim(
            $request->search
        );

        $query->where(function ($q) use ($search) {

            $q->where(
                'renewal_no',
                'like',
                "%{$search}%"
            )

            ->orWhereHas(
                'bookIssue',
                function ($q) use ($search) {

                    $q->where(
                        'issue_no',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | From Date
    |--------------------------------------------------------------------------
    */

    if ($request->filled('from_date')) {

        $query->whereDate(
            'renewal_date',
            '>=',
            $request->from_date
        );
    }

    /*
    |--------------------------------------------------------------------------
    | To Date
    |--------------------------------------------------------------------------
    */

    if ($request->filled('to_date')) {

        $query->whereDate(
            'renewal_date',
            '<=',
            $request->to_date
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $results = $query
        ->latest('renewal_date')
        ->paginate(
            $request->integer(
                'per_page',
                20
            )
        );

    /*
    |--------------------------------------------------------------------------
    | Prepare Renewal Data
    |--------------------------------------------------------------------------
    */

    $results->getCollection()
        ->transform(function ($renewal) use ($schoolId) {

            /*
            |--------------------------------------------------------------------------
            | Default
            |--------------------------------------------------------------------------
            */

            $renewal->book_issue = null;


            /*
            |--------------------------------------------------------------------------
            | Get Book Issue
            |--------------------------------------------------------------------------
            */

            if (!empty($renewal->book_issue_id)) {

                $issue =
                    BookIssue::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $renewal->book_issue_id
                    )
                    ->first();


                if ($issue) {

                    /*
                    |----------------------------------------------------------
                    | Library Member
                    |----------------------------------------------------------
                    */

                    $issue->library_member = null;

                    if (
                        !empty(
                            $issue->library_member_id
                        )
                    ) {

                        $issue->library_member =
                            LibraryMember::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->library_member_id
                            )
                            ->first();
                    }


                    /*
                    |----------------------------------------------------------
                    | Book
                    |----------------------------------------------------------
                    */

                    $issue->book = null;

                    if (
                        !empty(
                            $issue->book_id
                        )
                    ) {

                        $issue->book =
                            Book::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->book_id
                            )
                            ->first();
                    }


                    /*
                    |----------------------------------------------------------
                    | Book Copy
                    |----------------------------------------------------------
                    */

                    $issue->book_copy = null;

                    if (
                        !empty(
                            $issue->book_copy_id
                        )
                    ) {

                        $issue->book_copy =
                            BookCopy::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->book_copy_id
                            )
                            ->first();
                    }


                    /*
                    |----------------------------------------------------------
                    | Attach Issue
                    |----------------------------------------------------------
                    */

                    $renewal->book_issue =
                        $issue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Backward Compatible Fields
            |--------------------------------------------------------------------------
            */

            $renewal->issue =
                $renewal->book_issue;

            $renewal->member =
                $renewal->book_issue?->library_member;

            $renewal->book =
                $renewal->book_issue?->book;

            $renewal->copy =
                $renewal->book_issue?->book_copy;


            return $renewal;
        });


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | Lost / Damaged
    |--------------------------------------------------------------------------
    */

  public function lostDamaged(Request $request)
{
    $schoolId = $this->schoolId();

    $query = LostDamagedBook::query()
        ->where(
            'school_id',
            $schoolId
        );


    /*
    |--------------------------------------------------------------------------
    | Type
    |--------------------------------------------------------------------------
    */

    if ($request->filled('type')) {

        $query->where(
            'type',
            $request->type
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = trim(
            $request->search
        );

        $query->where(function ($q) use ($search) {

            $q->where(
                'type',
                'like',
                "%{$search}%"
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
                'libraryMember',
                function ($q) use ($search) {

                    $q->where(
                        'member_no',
                        'like',
                        "%{$search}%"
                    );
                }
            );

        });
    }


    /*
    |--------------------------------------------------------------------------
    | From Date
    |--------------------------------------------------------------------------
    */

    if ($request->filled('from_date')) {

        $query->whereDate(
            'created_at',
            '>=',
            $request->from_date
        );
    }


    /*
    |--------------------------------------------------------------------------
    | To Date
    |--------------------------------------------------------------------------
    */

    if ($request->filled('to_date')) {

        $query->whereDate(
            'created_at',
            '<=',
            $request->to_date
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $results = $query
        ->latest()
        ->paginate(
            $request->integer(
                'per_page',
                20
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Prepare Response
    |--------------------------------------------------------------------------
    */

    $results->getCollection()
        ->transform(function ($item) use ($schoolId) {

            /*
            |--------------------------------------------------------------------------
            | Default values
            |--------------------------------------------------------------------------
            */

            $item->library_member = null;
            $item->book = null;
            $item->book_copy = null;
            $item->book_issue = null;


            /*
            |--------------------------------------------------------------------------
            | Library Member
            |--------------------------------------------------------------------------
            */

            if (!empty($item->library_member_id)) {

                $item->library_member =
                    LibraryMember::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $item->library_member_id
                    )
                    ->first();
            }


            /*
            |--------------------------------------------------------------------------
            | Book
            |--------------------------------------------------------------------------
            */

            if (!empty($item->book_id)) {

                $item->book =
                    Book::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $item->book_id
                    )
                    ->first();
            }


            /*
            |--------------------------------------------------------------------------
            | Book Copy
            |--------------------------------------------------------------------------
            */

            if (!empty($item->book_copy_id)) {

                $item->book_copy =
                    BookCopy::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $item->book_copy_id
                    )
                    ->first();


                /*
                |--------------------------------------------------------------
                | If Book ID is not directly stored on Lost/Damaged record,
                | get it from the copy.
                |--------------------------------------------------------------
                */

                if (
                    !$item->book &&
                    $item->book_copy &&
                    !empty($item->book_copy->book_id)
                ) {

                    $item->book =
                        Book::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'id',
                            $item->book_copy->book_id
                        )
                        ->first();
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Book Issue
            |--------------------------------------------------------------------------
            */

            if (!empty($item->book_issue_id)) {

                $issue =
                    BookIssue::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $item->book_issue_id
                    )
                    ->first();


                if ($issue) {

                    /*
                    |----------------------------------------------------------
                    | Issue Member
                    |----------------------------------------------------------
                    */

                    if (
                        !$item->library_member &&
                        !empty($issue->library_member_id)
                    ) {

                        $item->library_member =
                            LibraryMember::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->library_member_id
                            )
                            ->first();
                    }


                    /*
                    |----------------------------------------------------------
                    | Issue Book
                    |----------------------------------------------------------
                    */

                    if (
                        !$item->book &&
                        !empty($issue->book_id)
                    ) {

                        $item->book =
                            Book::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->book_id
                            )
                            ->first();
                    }


                    /*
                    |----------------------------------------------------------
                    | Issue Copy
                    |----------------------------------------------------------
                    */

                    if (
                        !$item->book_copy &&
                        !empty($issue->book_copy_id)
                    ) {

                        $item->book_copy =
                            BookCopy::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->book_copy_id
                            )
                            ->first();
                    }


                    /*
                    |----------------------------------------------------------
                    | Add Issue
                    |----------------------------------------------------------
                    */

                    $item->book_issue = $issue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Keep backward-compatible names
            |--------------------------------------------------------------------------
            */

            $item->member =
                $item->library_member;

            $item->copy =
                $item->book_copy;


            return $item;
        });


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | Outstanding Fines
    |--------------------------------------------------------------------------
    */

    public function outstandingFines(Request $request)
{
    $schoolId = $this->schoolId();

    $query = LibraryFine::query()
        ->where('school_id', $schoolId)
        ->whereIn('status', [
            'Pending',
            'Partially Paid'
        ]);

    /*
    |--------------------------------------------------------------------------
    | Fine Type
    |--------------------------------------------------------------------------
    */

    if ($request->filled('fine_type')) {

        $query->where(
            'fine_type',
            $request->fine_type
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Date Filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('from_date')) {

        $query->whereDate(
            'created_at',
            '>=',
            $request->from_date
        );
    }

    if ($request->filled('to_date')) {

        $query->whereDate(
            'created_at',
            '<=',
            $request->to_date
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $results = $query
        ->latest()
        ->paginate(
            $request->integer(
                'per_page',
                20
            )
        );

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    $results->getCollection()
        ->transform(function ($fine) use ($schoolId) {

            /*
            |--------------------------------------------------------------------------
            | Library Member
            |--------------------------------------------------------------------------
            */

            $fine->library_member = null;

            if (!empty($fine->library_member_id)) {

                $fine->library_member =
                    LibraryMember::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $fine->library_member_id
                    )
                    ->first();
            }


            /*
            |--------------------------------------------------------------------------
            | Book Issue
            |--------------------------------------------------------------------------
            */

            $fine->book_issue = null;

            if (!empty($fine->book_issue_id)) {

                $issue =
                    BookIssue::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $fine->book_issue_id
                    )
                    ->first();

                if ($issue) {

                    /*
                    |----------------------------------------------------------
                    | Attach Book
                    |----------------------------------------------------------
                    */

                    $issue->book = null;

                    if (!empty($issue->book_id)) {

                        $issue->book =
                            Book::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->book_id
                            )
                            ->first();
                    }


                    /*
                    |----------------------------------------------------------
                    | Attach Copy
                    |----------------------------------------------------------
                    */

                    $issue->book_copy = null;

                    if (!empty($issue->book_copy_id)) {

                        $issue->book_copy =
                            BookCopy::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->book_copy_id
                            )
                            ->first();
                    }


                    /*
                    |----------------------------------------------------------
                    | Attach Member To Issue
                    |----------------------------------------------------------
                    */

                    $issue->library_member = null;

                    if (!empty($issue->library_member_id)) {

                        $issue->library_member =
                            LibraryMember::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $issue->library_member_id
                            )
                            ->first();
                    }


                    $fine->book_issue = $issue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Paid Amount
            |--------------------------------------------------------------------------
            */

            $paid = FineCollection::where(
                'school_id',
                $schoolId
            )
            ->where(
                'library_fine_id',
                $fine->id
            )
            ->sum('amount');


            $fine->paid_amount =
                (float) $paid;


            /*
            |--------------------------------------------------------------------------
            | Due Amount
            |--------------------------------------------------------------------------
            |
            | Do NOT use a database column called due_amount.
            | Calculate it from payable_amount - paid amount.
            |
            |--------------------------------------------------------------------------
            */

            $payable =
                (float) $fine->payable_amount;

            $fine->due_amount =
                max(
                    0,
                    $payable - (float) $paid
                );


            return $fine;
        });


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | Fine Collection
    |--------------------------------------------------------------------------
    */

    public function fineCollection(Request $request)
{
    $schoolId = $this->schoolId();

    $query = FineCollection::query()
        ->where('school_id', $schoolId);

    /*
    |--------------------------------------------------------------------------
    | Payment Method
    |--------------------------------------------------------------------------
    */
    if ($request->filled('payment_method')) {

        $query->where(
            'payment_mode',
            $request->payment_method
        );
    }

    /*
    |--------------------------------------------------------------------------
    | From Date
    |--------------------------------------------------------------------------
    */
    if ($request->filled('from_date')) {

        $query->whereDate(
            'collection_date',
            '>=',
            $request->from_date
        );
    }

    /*
    |--------------------------------------------------------------------------
    | To Date
    |--------------------------------------------------------------------------
    */
    if ($request->filled('to_date')) {

        $query->whereDate(
            'collection_date',
            '<=',
            $request->to_date
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    $results = $query
        ->latest('collection_date')
        ->paginate(
            $request->integer('per_page', 20)
        );

    /*
    |--------------------------------------------------------------------------
    | Prepare Report Data
    |--------------------------------------------------------------------------
    */
    $results->getCollection()->transform(
        function ($collection) use ($schoolId) {

            /*
            |--------------------------------------------------------------------------
            | Library Fine
            |--------------------------------------------------------------------------
            */

            $collection->library_fine = null;

            if (!empty($collection->library_fine_id)) {

                $collection->library_fine =
                    LibraryFine::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $collection->library_fine_id
                    )
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | Library Member
            |--------------------------------------------------------------------------
            */

            $collection->library_member = null;

            if (!empty($collection->library_member_id)) {

                $collection->library_member =
                    LibraryMember::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $collection->library_member_id
                    )
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | Normalize Fields For Vue
            |--------------------------------------------------------------------------
            */

            // Vue expects payment_date
            $collection->payment_date =
                $collection->collection_date;

            // Vue expects payment_method
            $collection->payment_method =
                $collection->payment_mode;

            /*
            |--------------------------------------------------------------------------
            | Reference Number
            |--------------------------------------------------------------------------
            |
            | Use whichever field exists in your FineCollection model/table.
            |
            */

            $collection->reference_no =
                $collection->reference_no
                ?? $collection->transaction_no
                ?? $collection->receipt_no
                ?? null;

            /*
            |--------------------------------------------------------------------------
            | Collector
            |--------------------------------------------------------------------------
            */

            $collection->collector = null;

            if (!empty($collection->collected_by)) {

                $collection->collector =
                    \App\Models\User::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $collection->collected_by
                    )
                    ->first();
            }

            return $collection;
        }
    );

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}
}