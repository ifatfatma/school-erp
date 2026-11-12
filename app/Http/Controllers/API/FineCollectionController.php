<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FineCollection;
use App\Models\LibraryFine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FineCollectionController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = FineCollection::query()
            ->with([
                'fine:id,fine_no,fine_type,fine_amount,payable_amount,status,book_id',
                'fine.book:id,title',
                'member:id,member_no,member_type,student_id,staff_id',
                'member.student:id,first_name,last_name,admission_no',
                'member.staff:id,first_name,last_name',
                'collector:id,name',
            ])
            ->where('school_id', $schoolId);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('collection_no', 'like', "%{$search}%")
                  ->orWhere('receipt_no', 'like', "%{$search}%")
                  ->orWhere('transaction_no', 'like', "%{$search}%")
                  ->orWhereHas('fine', function ($fq) use ($search) {
                      $fq->where('fine_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('member_no', 'like', "%{$search}%")
                         ->orWhereHas('student', function ($sq) use ($search) {
                             $sq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('collection_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('collection_date', '<=', $request->date_to);
        }

        $perPage = (int) $request->input('per_page', 20);
        $collections = $query->latest('id')->paginate($perPage);

        $stats = [
            'total_collected' => (clone $query)->sum('amount'),
            'today_collected' => (clone $query)->whereDate('collection_date', date('Y-m-d'))->sum('amount'),
            'this_month' => (clone $query)->where('collection_date', 'like', date('Y-m') . '%')->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $collections->items(),
            'stats' => $stats,
            'meta' => [
                'current_page' => $collections->currentPage(),
                'last_page' => $collections->lastPage(),
                'per_page' => $collections->perPage(),
                'total' => $collections->total(),
            ],
        ]);
    }

    public function pendingFines()
    {
        $schoolId = $this->schoolId();

        $fines = LibraryFine::with([
            'book:id,title',
            'libraryMember:id,member_no,member_type,student_id,staff_id',
            'libraryMember.student:id,first_name,last_name,admission_no',
            'libraryMember.staff:id,first_name,last_name',
            'collections:id,library_fine_id,amount',
        ])
            ->where('school_id', $schoolId)
            ->whereIn('status', ['Pending', 'Partially Paid'])
            ->get()
            ->map(function ($f) {
                $collected = $f->collections->sum('amount');
                $remaining = max(0, (float) $f->payable_amount - $collected);
                return [
                    'id' => $f->id,
                    'fine_no' => $f->fine_no,
                    'fine_type' => $f->fine_type,
                    'payable_amount' => (float) $f->payable_amount,
                    'collected_amount' => $collected,
                    'remaining_amount' => $remaining,
                    'status' => $f->status,
                    'member_id' => $f->library_member_id,
                    'member_no' => $f->libraryMember?->member_no,
                    'member_name' => $f->libraryMember?->display_name ?? 'Member #' . $f->library_member_id,
                    'book_title' => $f->book?->title,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $fines,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'library_fine_id' => 'required|exists:library_fines,id',
            'amount' => 'required|numeric|min:0.01',
            'collection_date' => 'required|date',
            'payment_mode' => 'required|string|in:Cash,Card,UPI,Bank Transfer,Cheque,Other',
            'transaction_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $schoolId = $this->schoolId();
        $fine = LibraryFine::where('school_id', $schoolId)->findOrFail($validated['library_fine_id']);

        $collection = DB::transaction(function () use ($schoolId, $fine, $validated) {
            $validated['school_id'] = $schoolId;
            $validated['library_member_id'] = $fine->library_member_id;
            $validated['collection_no'] = 'FC-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $validated['receipt_no'] = 'REC-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $validated['collected_by'] = auth('sanctum')->id();

            $record = FineCollection::create($validated);

            // Re-calculate fine status
            $totalCollected = FineCollection::where('library_fine_id', $fine->id)->sum('amount');
            if ($totalCollected >= (float) $fine->payable_amount) {
                $fine->status = 'Paid';
            } else {
                $fine->status = 'Partially Paid';
            }
            $fine->save();

            return $record;
        });

        $collection->load([
            'fine.book',
            'member.student',
            'member.staff',
            'collector',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fine payment collected successfully.',
            'data' => $collection,
        ], 201);
    }

    public function show(int $id)
    {
        $collection = FineCollection::with([
            'fine.book',
            'member.student',
            'member.staff',
            'collector',
        ])
            ->where('school_id', $this->schoolId())
            ->find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $collection,
        ]);
    }

    public function destroy(int $id)
    {
        $collection = FineCollection::where('school_id', $this->schoolId())->find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection record not found.',
            ], 404);
        }

        DB::transaction(function () use ($collection) {
            $fineId = $collection->library_fine_id;
            $collection->delete();

            // Re-calculate fine status
            $fine = LibraryFine::find($fineId);
            if ($fine) {
                $totalCollected = FineCollection::where('library_fine_id', $fine->id)->sum('amount');
                if ($totalCollected <= 0) {
                    $fine->status = 'Pending';
                } elseif ($totalCollected >= (float) $fine->payable_amount) {
                    $fine->status = 'Paid';
                } else {
                    $fine->status = 'Partially Paid';
                }
                $fine->save();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Collection record deleted.',
        ]);
    }
}
