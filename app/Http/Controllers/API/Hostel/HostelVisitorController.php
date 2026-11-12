<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelStudent;
use App\Models\HostelVisitor;
use App\Models\Student;
use Illuminate\Http\Request;

class HostelVisitorController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $query = HostelVisitor::query()
            ->with([
                'student:id,admission_no,first_name,last_name,class_id',
                'student.class:id,name',
            ])
            ->where('school_id', $this->schoolId());

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('visitor_name', 'like', "%{$search}%")
                  ->orWhere('visitor_phone', 'like', "%{$search}%")
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

        if ($request->filled('date')) {
            $query->whereDate('visit_date', $request->date);
        }

        $perPage = (int) $request->input('per_page', 20);
        $visitors = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $visitors->items(),
            'meta' => [
                'current_page' => $visitors->currentPage(),
                'last_page' => $visitors->lastPage(),
                'per_page' => $visitors->perPage(),
                'total' => $visitors->total(),
            ],
        ]);
    }

    public function students()
    {
        $students = HostelStudent::with('student:id,admission_no,first_name,last_name')
            ->where('school_id', $this->schoolId())
            ->where('status', 'active')
            ->get()
            ->pluck('student')
            ->filter();

        return response()->json([
            'success' => true,
            'data' => $students->values(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'visitor_name' => 'required|string|max:255',
            'relation' => 'required|string|max:100',
            'visitor_phone' => 'nullable|string|max:50',
            'visit_date' => 'required|date',
            'in_time' => 'nullable|string',
            'out_time' => 'nullable|string',
            'id_proof_type' => 'nullable|string',
            'id_proof_number' => 'nullable|string',
            'purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['school_id'] = $this->schoolId();
        $validated['status'] = empty($validated['out_time']) ? 'inside' : 'checked_out';

        $visitor = HostelVisitor::create($validated);
        $visitor->load('student.class');

        return response()->json([
            'success' => true,
            'message' => 'Visitor logged successfully.',
            'data' => $visitor,
        ], 201);
    }

    public function checkout(Request $request, int $id)
    {
        $visitor = HostelVisitor::where('school_id', $this->schoolId())->find($id);

        if (!$visitor) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor record not found.',
            ], 404);
        }

        $outTime = $request->input('out_time', date('h:i A'));
        $visitor->update([
            'out_time' => $outTime,
            'status' => 'checked_out',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visitor checked out successfully.',
            'data' => $visitor,
        ]);
    }

    public function destroy(int $id)
    {
        $visitor = HostelVisitor::where('school_id', $this->schoolId())->find($id);

        if (!$visitor) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor record not found.',
            ], 404);
        }

        $visitor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Visitor record deleted.',
        ]);
    }
}
