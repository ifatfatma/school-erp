<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelComplaint;
use App\Models\HostelRoom;
use App\Models\HostelStudent;
use Illuminate\Http\Request;

class HostelComplaintController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $query = HostelComplaint::query()
            ->with([
                'student:id,admission_no,first_name,last_name,class_id',
                'student.class:id,name',
                'room:id,room_no,hostel_building_id',
                'room.building:id,name',
            ])
            ->where('school_id', $this->schoolId());

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $perPage = (int) $request->input('per_page', 20);
        $complaints = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $complaints->items(),
            'meta' => [
                'current_page' => $complaints->currentPage(),
                'last_page' => $complaints->lastPage(),
                'per_page' => $complaints->perPage(),
                'total' => $complaints->total(),
            ],
        ]);
    }

    public function dropdowns()
    {
        $schoolId = $this->schoolId();

        $students = HostelStudent::with('student:id,admission_no,first_name,last_name')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->get()
            ->pluck('student')
            ->filter();

        $rooms = HostelRoom::with('building:id,name')
            ->where('school_id', $schoolId)
            ->where('status', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $students->values(),
                'rooms' => $rooms,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'hostel_room_id' => 'nullable|exists:hostel_rooms,id',
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'description' => 'required|string',
        ]);

        $validated['school_id'] = $this->schoolId();
        $validated['status'] = 'pending';

        $complaint = HostelComplaint::create($validated);
        $complaint->load(['student', 'room.building']);

        return response()->json([
            'success' => true,
            'message' => 'Complaint lodged successfully.',
            'data' => $complaint,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $complaint = HostelComplaint::where('school_id', $this->schoolId())->find($id);

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found.',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string',
        ]);

        if (in_array($validated['status'], ['resolved', 'closed']) && !$complaint->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $complaint->update($validated);
        $complaint->load(['student', 'room.building']);

        return response()->json([
            'success' => true,
            'message' => 'Complaint updated successfully.',
            'data' => $complaint,
        ]);
    }

    public function destroy(int $id)
    {
        $complaint = HostelComplaint::where('school_id', $this->schoolId())->find($id);

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found.',
            ], 404);
        }

        $complaint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complaint deleted successfully.',
        ]);
    }
}
