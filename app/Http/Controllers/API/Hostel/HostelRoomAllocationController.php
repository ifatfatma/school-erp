<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelBed;
use App\Models\HostelRoom;
use App\Models\HostelRoomAllocation;
use App\Models\HostelStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelRoomAllocationController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $query = HostelRoomAllocation::query()
            ->with([
                'student:id,admission_no,first_name,last_name,class_id,section_id',
                'student.class:id,name',
                'student.section:id,name',
                'room:id,room_no,floor_no,room_type,hostel_building_id',
                'room.building:id,name,code',
                'bed:id,bed_no',
            ])
            ->where('school_id', $this->schoolId());

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('admission_no', 'like', "%{$search}%");
                })->orWhereHas('room', function ($rq) use ($search) {
                    $rq->where('room_no', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('hostel_room_id')) {
            $query->where('hostel_room_id', $request->hostel_room_id);
        }

        $perPage = (int) $request->input('per_page', 20);
        $allocations = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $allocations->items(),
            'meta' => [
                'current_page' => $allocations->currentPage(),
                'last_page' => $allocations->lastPage(),
                'per_page' => $allocations->perPage(),
                'total' => $allocations->total(),
            ],
        ]);
    }

    public function dropdowns()
    {
        $schoolId = $this->schoolId();

        $students = Student::where('school_id', $schoolId)
            ->where('status', true)
            ->select('id', 'admission_no', 'first_name', 'last_name', 'class_id')
            ->with('class:id,name')
            ->orderBy('first_name')
            ->get();

        $rooms = HostelRoom::where('school_id', $schoolId)
            ->where('status', true)
            ->with(['building:id,name', 'beds' => function ($q) {
                $q->where('status', 'available');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $students,
                'rooms' => $rooms,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'hostel_room_id' => 'required|exists:hostel_rooms,id',
            'hostel_bed_id' => 'nullable|exists:hostel_beds,id',
            'allocated_date' => 'required|date',
            'monthly_fee' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $validated['school_id'] = $this->schoolId();
        $validated['status'] = 'active';

        $allocation = DB::transaction(function () use ($validated) {
            // Check if student already has an active allocation
            $existing = HostelRoomAllocation::where('school_id', $validated['school_id'])
                ->where('student_id', $validated['student_id'])
                ->where('status', 'active')
                ->first();

            if ($existing) {
                $existing->update([
                    'status' => 'vacated',
                    'vacated_date' => date('Y-m-d'),
                ]);
                if ($existing->hostel_bed_id) {
                    HostelBed::where('id', $existing->hostel_bed_id)->update(['status' => 'available']);
                }
                HostelRoom::where('id', $existing->hostel_room_id)->where('occupied_beds', '>', 0)->decrement('occupied_beds');
            }

            $record = HostelRoomAllocation::create($validated);

            if (!empty($validated['hostel_bed_id'])) {
                HostelBed::where('id', $validated['hostel_bed_id'])->update(['status' => 'occupied']);
            }
            HostelRoom::where('id', $validated['hostel_room_id'])->increment('occupied_beds');

            // Sync with hostel_students if registered
            HostelStudent::updateOrCreate(
                ['school_id' => $validated['school_id'], 'student_id' => $validated['student_id']],
                [
                    'hostel_room_id' => $validated['hostel_room_id'],
                    'hostel_bed_id' => $validated['hostel_bed_id'] ?? null,
                    'status' => 'active',
                ]
            );

            return $record;
        });

        $allocation->load(['student.class', 'room.building', 'bed']);

        return response()->json([
            'success' => true,
            'message' => 'Room allocated successfully.',
            'data' => $allocation,
        ], 201);
    }

    public function vacate(Request $request, int $id)
    {
        $allocation = HostelRoomAllocation::where('school_id', $this->schoolId())->find($id);

        if (!$allocation) {
            return response()->json([
                'success' => false,
                'message' => 'Allocation not found.',
            ], 404);
        }

        DB::transaction(function () use ($allocation, $request) {
            $allocation->update([
                'status' => 'vacated',
                'vacated_date' => $request->input('vacated_date', date('Y-m-d')),
                'remarks' => $request->input('remarks', $allocation->remarks),
            ]);

            if ($allocation->hostel_bed_id) {
                HostelBed::where('id', $allocation->hostel_bed_id)->update(['status' => 'available']);
            }
            HostelRoom::where('id', $allocation->hostel_room_id)->where('occupied_beds', '>', 0)->decrement('occupied_beds');

            HostelStudent::where('school_id', $allocation->school_id)
                ->where('student_id', $allocation->student_id)
                ->update([
                    'status' => 'vacated',
                    'hostel_room_id' => null,
                    'hostel_bed_id' => null,
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Room vacated successfully.',
        ]);
    }

    public function destroy(int $id)
    {
        $allocation = HostelRoomAllocation::where('school_id', $this->schoolId())->find($id);

        if (!$allocation) {
            return response()->json([
                'success' => false,
                'message' => 'Allocation not found.',
            ], 404);
        }

        DB::transaction(function () use ($allocation) {
            if ($allocation->status === 'active') {
                if ($allocation->hostel_bed_id) {
                    HostelBed::where('id', $allocation->hostel_bed_id)->update(['status' => 'available']);
                }
                HostelRoom::where('id', $allocation->hostel_room_id)->where('occupied_beds', '>', 0)->decrement('occupied_beds');
            }
            $allocation->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Allocation record deleted.',
        ]);
    }
}
