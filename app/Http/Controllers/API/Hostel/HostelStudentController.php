<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelBed;
use App\Models\HostelRoom;
use App\Models\HostelStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelStudentController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $query = HostelStudent::query()
            ->with([
                'student:id,admission_no,first_name,last_name,gender,mobile,class_id,section_id',
                'student.class:id,name',
                'student.section:id,name',
                'hostel:id,name,code',
                'room:id,room_no,room_type,hostel_building_id',
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
                       ->orWhere('admission_no', 'like', "%{$search}%")
                       ->orWhere('mobile', 'like', "%{$search}%");
                })->orWhereHas('room', function ($rq) use ($search) {
                    $rq->where('room_no', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->filled('hostel_room_id')) {
            $query->where('hostel_room_id', $request->hostel_room_id);
        }

        $perPage = (int) $request->input('per_page', 20);
        $students = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $students->items(),
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ],
        ]);
    }

    public function dropdowns()
    {
        $schoolId = $this->schoolId();

        $students = Student::where('school_id', $schoolId)
            ->where('status', true)
            ->select('id', 'admission_no', 'first_name', 'last_name', 'gender', 'class_id')
            ->with('class:id,name')
            ->orderBy('first_name')
            ->get();

        $hostels = Hostel::where('school_id', $schoolId)
            ->where('status', true)
            ->select('id', 'name', 'type')
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
                'hostels' => $hostels,
                'rooms' => $rooms,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'hostel_id' => 'nullable|exists:hostels,id',
            'hostel_room_id' => 'nullable|exists:hostel_rooms,id',
            'hostel_bed_id' => 'nullable|exists:hostel_beds,id',
            'admission_date' => 'nullable|date',
            'status' => 'required|string|in:active,vacated,suspended',
            'food_preference' => 'nullable|string|in:veg,non-veg,both',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'medical_conditions' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['school_id'] = $this->schoolId();
        $validated['admission_date'] = $validated['admission_date'] ?? date('Y-m-d');

        $record = DB::transaction(function () use ($validated) {
            $student = HostelStudent::create($validated);

            if (!empty($validated['hostel_bed_id'])) {
                HostelBed::where('id', $validated['hostel_bed_id'])->update(['status' => 'occupied']);
            }
            if (!empty($validated['hostel_room_id'])) {
                HostelRoom::where('id', $validated['hostel_room_id'])->increment('occupied_beds');
            }

            return $student;
        });

        $record->load([
            'student',
            'hostel',
            'room.building',
            'bed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hostel student registered successfully.',
            'data' => $record,
        ], 201);
    }

    public function show(int $id)
    {
        $student = HostelStudent::with([
            'student.class',
            'student.section',
            'hostel',
            'room.building',
            'bed',
            'allocations',
            'fees',
            'attendances',
            'complaints',
            'visitors',
        ])->where('school_id', $this->schoolId())->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel student not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $student = HostelStudent::where('school_id', $this->schoolId())->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel student not found.',
            ], 404);
        }

        $validated = $request->validate([
            'hostel_id' => 'nullable|exists:hostels,id',
            'hostel_room_id' => 'nullable|exists:hostel_rooms,id',
            'hostel_bed_id' => 'nullable|exists:hostel_beds,id',
            'admission_date' => 'nullable|date',
            'status' => 'required|string|in:active,vacated,suspended',
            'food_preference' => 'nullable|string|in:veg,non-veg,both',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'medical_conditions' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($student, $validated) {
            $oldBedId = $student->hostel_bed_id;
            $newBedId = $validated['hostel_bed_id'] ?? null;
            $oldRoomId = $student->hostel_room_id;
            $newRoomId = $validated['hostel_room_id'] ?? null;

            if ($oldBedId && $oldBedId != $newBedId) {
                HostelBed::where('id', $oldBedId)->update(['status' => 'available']);
            }
            if ($oldRoomId && $oldRoomId != $newRoomId) {
                HostelRoom::where('id', $oldRoomId)->where('occupied_beds', '>', 0)->decrement('occupied_beds');
            }

            if ($newBedId && $oldBedId != $newBedId) {
                HostelBed::where('id', $newBedId)->update(['status' => 'occupied']);
            }
            if ($newRoomId && $oldRoomId != $newRoomId) {
                HostelRoom::where('id', $newRoomId)->increment('occupied_beds');
            }

            $student->update($validated);
        });

        $student->load(['student', 'hostel', 'room.building', 'bed']);

        return response()->json([
            'success' => true,
            'message' => 'Hostel student updated successfully.',
            'data' => $student,
        ]);
    }

    public function destroy(int $id)
    {
        $student = HostelStudent::where('school_id', $this->schoolId())->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel student not found.',
            ], 404);
        }

        DB::transaction(function () use ($student) {
            if ($student->hostel_bed_id) {
                HostelBed::where('id', $student->hostel_bed_id)->update(['status' => 'available']);
            }
            if ($student->hostel_room_id) {
                HostelRoom::where('id', $student->hostel_room_id)->where('occupied_beds', '>', 0)->decrement('occupied_beds');
            }
            $student->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Hostel student removed successfully.',
        ]);
    }
}
