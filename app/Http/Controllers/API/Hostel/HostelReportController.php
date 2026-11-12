<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelAttendance;
use App\Models\HostelComplaint;
use App\Models\HostelFee;
use App\Models\HostelRoom;
use App\Models\HostelStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelReportController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function occupancy(Request $request)
    {
        $schoolId = $this->schoolId();

        $rooms = HostelRoom::with(['building:id,name,code', 'beds'])
            ->where('school_id', $schoolId)
            ->get();

        $summary = [
            'total_rooms' => $rooms->count(),
            'total_capacity' => $rooms->sum('capacity'),
            'total_occupied' => $rooms->sum('occupied_beds'),
            'total_available' => max(0, $rooms->sum('capacity') - $rooms->sum('occupied_beds')),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $rooms,
        ]);
    }

    public function students(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = HostelStudent::with([
            'student:id,admission_no,first_name,last_name,gender,mobile,class_id',
            'student.class:id,name',
            'hostel:id,name',
            'room:id,room_no,floor_no,hostel_building_id',
            'room.building:id,name',
            'bed:id,bed_no',
        ])
            ->where('school_id', $schoolId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        $students = $query->get();

        return response()->json([
            'success' => true,
            'total' => $students->count(),
            'data' => $students,
        ]);
    }

    public function fees(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = HostelFee::with([
            'student:id,admission_no,first_name,last_name',
            'room:id,room_no',
        ])
            ->where('school_id', $schoolId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $fees = $query->get();

        $summary = [
            'total_billed' => $fees->sum('amount'),
            'total_discount' => $fees->sum('discount'),
            'total_fine' => $fees->sum('fine'),
            'total_collected' => $fees->sum('paid_amount'),
            'total_due' => max(0, $fees->sum('amount') - $fees->sum('paid_amount')),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $fees,
        ]);
    }

    public function attendance(Request $request)
    {
        $schoolId = $this->schoolId();
        $dateFrom = $request->input('date_from', date('Y-m-01'));
        $dateTo = $request->input('date_to', date('Y-m-d'));

        $query = HostelAttendance::with([
            'student:id,admission_no,first_name,last_name',
            'room:id,room_no',
        ])
            ->where('school_id', $schoolId)
            ->whereBetween('attendance_date', [$dateFrom, $dateTo]);

        $attendances = $query->get();

        $summary = [
            'total_records' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'leave' => $attendances->where('status', 'leave')->count(),
            'late' => $attendances->where('status', 'late')->count(),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $attendances,
        ]);
    }

    public function complaints(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = HostelComplaint::with([
            'student:id,admission_no,first_name,last_name',
            'room:id,room_no',
        ])
            ->where('school_id', $schoolId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->get();

        $summary = [
            'total' => $complaints->count(),
            'pending' => $complaints->where('status', 'pending')->count(),
            'in_progress' => $complaints->where('status', 'in_progress')->count(),
            'resolved' => $complaints->where('status', 'resolved')->count(),
            'closed' => $complaints->where('status', 'closed')->count(),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $complaints,
        ]);
    }
}
