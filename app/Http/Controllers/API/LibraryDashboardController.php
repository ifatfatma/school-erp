<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\BookIssue;
use App\Models\BookRenewal;
use App\Models\BookReturn;
use App\Models\FineCollection;
use App\Models\LibraryFine;
use App\Models\LibraryMember;
use App\Models\LostDamagedBook;
use Illuminate\Http\Request;

class LibraryDashboardController extends Controller
{
    protected function schoolId(?Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        $totalBooks = Book::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $totalCopies = BookCopy::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $availableCopies = BookCopy::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->where('status', 'Available')->count();
        $issuedCopies = BookCopy::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->where('status', 'Issued')->count();
        $lostDamagedCopies = BookCopy::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->whereIn('status', ['Lost', 'Damaged'])->count();

        $totalMembers = LibraryMember::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $activeMembers = LibraryMember::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->where('status', true)->count();

        $activeIssues = BookIssue::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->where('status', 'Issued')->count();
        $overdueIssues = BookIssue::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('status', 'Issued')
            ->whereDate('due_date', '<', $today)
            ->count();

        $totalReturned = BookReturn::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $totalRenewals = BookRenewal::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();

        // Fines summary
        $totalFinesBilled = LibraryFine::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->sum('payable_amount');
        $totalFinesCollected = FineCollection::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->sum('amount');
        $finesPending = max(0, $totalFinesBilled - $totalFinesCollected);

        // Recent Issues
        $recentIssues = BookIssue::with([
            'book:id,title',
            'bookCopy:id,barcode,accession_no',
            'libraryMember:id,member_no,member_type,student_id,staff_id',
            'libraryMember.student:id,first_name,last_name,admission_no',
            'libraryMember.staff:id,first_name,last_name',
        ])
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->latest('id')
            ->limit(5)
            ->get();

        // Recent Returns
        $recentReturns = BookReturn::with([
            'book:id,title',
            'libraryMember:id,member_no,member_type,student_id,staff_id',
            'libraryMember.student:id,first_name,last_name',
            'libraryMember.staff:id,first_name,last_name',
        ])
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->latest('id')
            ->limit(5)
            ->get();

        // Overdue Books
        $overdueList = BookIssue::with([
            'book:id,title',
            'libraryMember:id,member_no,member_type,student_id,staff_id',
            'libraryMember.student:id,first_name,last_name',
            'libraryMember.staff:id,first_name,last_name',
        ])
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('status', 'Issued')
            ->whereDate('due_date', '<', $today)
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Recent Collections
        $recentCollections = FineCollection::with([
            'fine:id,fine_no,fine_type',
            'member:id,member_no,member_type,student_id,staff_id',
            'member.student:id,first_name,last_name',
            'member.staff:id,first_name,last_name',
        ])
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->latest('id')
            ->limit(5)
            ->get();

        // Categories breakdown
        $categories = BookCategory::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->withCount('books')
            ->limit(6)
            ->get(['id', 'name']);

        $res = [
            'success' => true,
            'data' => [
                'counts' => [
                    'total_books' => $totalBooks,
                    'total_copies' => $totalCopies,
                    'available_copies' => $availableCopies,
                    'issued_copies' => $issuedCopies,
                    'lost_damaged_copies' => $lostDamagedCopies,
                    'total_members' => $totalMembers,
                    'active_members' => $activeMembers,
                    'active_issues' => $activeIssues,
                    'overdue_issues' => $overdueIssues,
                    'total_returned' => $totalReturned,
                    'total_renewals' => $totalRenewals,
                ],
                'fines' => [
                    'total_billed' => (float) $totalFinesBilled,
                    'total_collected' => (float) $totalFinesCollected,
                    'pending' => (float) $finesPending,
                ],
                'recent_issues' => $recentIssues,
                'recent_returns' => $recentReturns,
                'overdue_list' => $overdueList,
                'recent_collections' => $recentCollections,
                'categories' => $categories,
            ],
        ];

        if ($this->isSuperAdmin($request)) {
            $res['all_schools'] = \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']);
        }

        return response()->json($res);
    }
}
