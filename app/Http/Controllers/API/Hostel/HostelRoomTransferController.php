<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelBed;
use App\Models\HostelRoom;
use App\Models\HostelRoomAllocation;
use App\Models\HostelRoomTransfer;
use App\Models\HostelStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelRoomTransferController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $query = HostelRoomTransfer::query()
            ->with([
                'student:id,admission_no,first_name,last_name,class_id,section_id',
                'student.class:id,name',
                'fromRoom:id,room_no,room_type,hostel_building_id',
                'fromRoom.building:id,name',
                'fromBed:id,bed_no',
                'toRoom:id,room_no,room_type,hostel_building_id',
                'toRoom.building:id,name',
                'toBed:id,bed_no',
            ])
            ->where('school_id', $this->schoolId());

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('admission_no', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->input('per_page', 20);
        $transfers = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transfers->items(),
            'meta' => [
                'current_page' => $transfers->currentPage(),
                'last_page' => $transfers->lastPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total(),
            ],
        ]);
    }

    public function dropdowns()
    {
        $schoolId = $this->schoolId();

        // Students currently allocated to a room
        $allocations = HostelRoomAllocation::with([
            'student:id,admission_no,first_name,last_name',
            'room:id,room_no,hostel_building_id',
            'room.building:id,name',
            'bed:id,bed_no',
        ])
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->get();

        // Rooms with available beds
        $rooms = HostelRoom::with([
            'building:id,name',
            'beds' => function ($q) {
                $q->where('status', 'available');
            }
        ])
            ->where('school_id', $schoolId)
            ->where('status', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'allocations' => $allocations,
                'rooms' => $rooms,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'from_room_id' => 'required|exists:hostel_rooms,id',
            'from_bed_id' => 'nullable|exists:hostel_beds,id',
            'to_room_id' => 'required|exists:hostel_rooms,id|different:from_room_id',
            'to_bed_id' => 'nullable|exists:hostel_beds,id',
            'transfer_date' => 'required|date',
            'reason' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['school_id'] = $this->schoolId();
        $validated['status'] = 'approved';
        $validated['approved_by'] = auth('sanctum')->id();

        $transfer = DB::transaction(function () use ($validated) {
            $record = HostelRoomTransfer::create($validated);

            // Vacate old allocation
            HostelRoomAllocation::where('school_id', $validated['school_id'])
                ->where('student_id', $validated['student_id'])
                ->where('status', 'active')
                ->update([
                    'status' => 'vacated',
                    'vacated_date' => $validated['transfer_date'],
                ]);

            if (!empty($validated['from_bed_id'])) {
                HostelBed::where('id', $validated['from_bed_id'])->update(['status' => 'available']);
            }
            HostelRoom::where('id', $validated['from_room_id'])->where('occupied_beds', '>', 0)->decrement('occupied_beds');

            // Create new allocation
            HostelRoomAllocation::create([
                'school_id' => $validated['school_id'],
                'student_id' => $validated['student_id'],
                'hostel_room_id' => $validated['to_room_id'],
                'hostel_bed_id' => $validated['to_bed_id'] ?? null,
                'allocated_date' => $validated['transfer_date'],
                'status' => 'active',
                'remarks' => 'Transferred: ' . ($validated['reason'] ?? ''),
            ]);

            if (!empty($validated['to_bed_id'])) {
                HostelBed::where('id', $validated['to_bed_id'])->update(['status' => 'occupied']);
            }
            HostelRoom::where('id', $validated['to_room_id'])->increment('occupied_beds');

            // Update hostel_students
            HostelStudent::where('school_id', $validated['school_id'])
                ->where('student_id', $validated['student_id'])
                ->update([
                    'hostel_room_id' => $validated['to_room_id'],
                    'hostel_bed_id' => $validated['to_bed_id'] ?? null,
                ]);

            return $record;
        });

        $transfer->load([
            'student',
            'fromRoom.building',
            'fromBed',
            'toRoom.building',
            'toBed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student transferred successfully.',
            'data' => $transfer,
        ], 201);
    }

    public function destroy(int $id)
    {
        $transfer = HostelRoomTransfer::where('school_id', $this->schoolId())->find($id);

        if (!$transfer) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer record not found.',
            ], 404);
        }

        $transfer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transfer record deleted.',
        ]);
    }
}
