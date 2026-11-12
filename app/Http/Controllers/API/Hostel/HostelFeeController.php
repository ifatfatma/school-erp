<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelFee;
use App\Models\HostelRoom;
use App\Models\HostelStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HostelFeeController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $query = HostelFee::query()
            ->with([
                'student:id,admission_no,first_name,last_name,class_id',
                'student.class:id,name',
                'room:id,room_no',
            ])
            ->where('school_id', $this->schoolId());

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('admission_no', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('fee_type')) {
            $query->where('fee_type', $request->fee_type);
        }

        $perPage = (int) $request->input('per_page', 20);
        $fees = $query->latest('id')->paginate($perPage);

        // Stats summary
        $stats = [
            'total_amount' => (clone $query)->sum('amount'),
            'total_paid' => (clone $query)->sum('paid_amount'),
            'total_due' => (clone $query)->where('status', '!=', 'paid')->sum('amount') - (clone $query)->where('status', '!=', 'paid')->sum('paid_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $fees->items(),
            'stats' => $stats,
            'meta' => [
                'current_page' => $fees->currentPage(),
                'last_page' => $fees->lastPage(),
                'per_page' => $fees->perPage(),
                'total' => $fees->total(),
            ],
        ]);
    }

    public function students()
    {
        $students = HostelStudent::with([
            'student:id,admission_no,first_name,last_name',
            'room:id,room_no,monthly_fee'
        ])
            ->where('school_id', $this->schoolId())
            ->where('status', 'active')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'hostel_room_id' => 'nullable|exists:hostel_rooms,id',
            'fee_type' => 'required|string|max:100',
            'month' => 'nullable|string|max:20',
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'fine' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $validated['school_id'] = $this->schoolId();
        $validated['invoice_no'] = 'HF-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $validated['paid_amount'] = 0;
        $validated['status'] = 'unpaid';

        $fee = HostelFee::create($validated);
        $fee->load(['student.class', 'room']);

        return response()->json([
            'success' => true,
            'message' => 'Hostel fee invoice generated successfully.',
            'data' => $fee,
        ], 201);
    }

    public function collect(Request $request, int $id)
    {
        $fee = HostelFee::where('school_id', $this->schoolId())->find($id);

        if (!$fee) {
            return response()->json([
                'success' => false,
                'message' => 'Fee invoice not found.',
            ], 404);
        }

        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'paid_date' => 'required|date',
            'payment_method' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $newPaid = $fee->paid_amount + $validated['paid_amount'];
        $netPayable = ($fee->amount - $fee->discount + $fee->fine);

        $status = 'partial';
        if ($newPaid >= $netPayable) {
            $status = 'paid';
        }

        $fee->update([
            'paid_amount' => $newPaid,
            'paid_date' => $validated['paid_date'],
            'payment_method' => $validated['payment_method'],
            'status' => $status,
            'remarks' => $validated['remarks'] ?? $fee->remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment collected successfully.',
            'data' => $fee,
        ]);
    }

    public function destroy(int $id)
    {
        $fee = HostelFee::where('school_id', $this->schoolId())->find($id);

        if (!$fee) {
            return response()->json([
                'success' => false,
                'message' => 'Fee invoice not found.',
            ], 404);
        }

        $fee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee record deleted.',
        ]);
    }
}
