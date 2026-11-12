<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelAttendance;
use App\Models\HostelStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelAttendanceController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();
        $date = $request->input('date', date('Y-m-d'));

        // Get all active hostel students
        $query = HostelStudent::with([
            'student:id,admission_no,first_name,last_name,gender,class_id,section_id',
            'student.class:id,name',
            'student.section:id,name',
            'room:id,room_no,floor_no,hostel_building_id',
            'room.building:id,name',
            'bed:id,bed_no',
        ])
            ->where('school_id', $schoolId)
            ->where('status', 'active');

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->filled('hostel_room_id')) {
            $query->where('hostel_room_id', $request->hostel_room_id);
        }

        $students = $query->get();

        // Get attendances for this date
        $attendances = HostelAttendance::where('school_id', $schoolId)
            ->where('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        $result = $students->map(function ($hs) use ($attendances) {
            $att = $attendances->get($hs->student_id);
            return [
                'hostel_student_id' => $hs->id,
                'student_id' => $hs->student_id,
                'student' => $hs->student,
                'room' => $hs->room,
                'bed' => $hs->bed,
                'status' => $att ? $att->status : 'present',
                'remarks' => $att ? $att->remarks : '',
                'attendance_id' => $att ? $att->id : null,
            ];
        });

        // Calculate counts
        $present = $result->where('status', 'present')->count();
        $absent = $result->where('status', 'absent')->count();
        $leave = $result->where('status', 'leave')->count();
        $late = $result->where('status', 'late')->count();

        return response()->json([
            'success' => true,
            'date' => $date,
            'data' => $result,
            'summary' => [
                'total' => $result->count(),
                'present' => $present,
                'absent' => $absent,
                'leave' => $leave,
                'late' => $late,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,leave,late',
            'attendances.*.remarks' => 'nullable|string',
        ]);

        $schoolId = $this->schoolId();
        $userId = auth('sanctum')->id();
        $date = $validated['date'];

        DB::transaction(function () use ($schoolId, $userId, $date, $validated) {
            foreach ($validated['attendances'] as $item) {
                HostelAttendance::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $item['student_id'],
                        'attendance_date' => $date,
                    ],
                    [
                        'status' => $item['status'],
                        'remarks' => $item['remarks'] ?? null,
                        'marked_by' => $userId,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Hostel attendance saved successfully.',
        ]);
    }
}
