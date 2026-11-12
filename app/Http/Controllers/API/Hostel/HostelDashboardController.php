<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelAttendance;
use App\Models\HostelBed;
use App\Models\HostelBuilding;
use App\Models\HostelComplaint;
use App\Models\HostelFee;
use App\Models\HostelRoom;
use App\Models\HostelRoomAllocation;
use App\Models\HostelStudent;
use App\Models\HostelVisitor;
use Illuminate\Http\Request;

class HostelDashboardController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index()
    {
        $schoolId = $this->schoolId();
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        $totalHostels = Hostel::where('school_id', $schoolId)->count();
        $totalBuildings = HostelBuilding::where('school_id', $schoolId)->count();
        $totalRooms = HostelRoom::where('school_id', $schoolId)->count();
        $totalBeds = HostelBed::where('school_id', $schoolId)->count();

        // If beds table isn't populated yet, calculate bed capacity from rooms
        $roomCapacity = HostelRoom::where('school_id', $schoolId)->sum('capacity');
        $roomOccupied = HostelRoom::where('school_id', $schoolId)->sum('occupied_beds');

        $occupiedBeds = HostelBed::where('school_id', $schoolId)->where('status', 'occupied')->count();
        if ($totalBeds === 0 && $roomCapacity > 0) {
            $effectiveCapacity = $roomCapacity;
            $effectiveOccupied = $roomOccupied;
        } else {
            $effectiveCapacity = $totalBeds;
            $effectiveOccupied = $occupiedBeds;
        }

        $availableBeds = max(0, $effectiveCapacity - $effectiveOccupied);
        $occupancyRate = $effectiveCapacity > 0 ? round(($effectiveOccupied / $effectiveCapacity) * 100, 1) : 0;

        $totalStudents = HostelStudent::where('school_id', $schoolId)->where('status', 'active')->count();

        // Attendance today
        $todayAttendances = HostelAttendance::where('school_id', $schoolId)
            ->where('attendance_date', $today)
            ->get();

        $presentToday = $todayAttendances->where('status', 'present')->count();
        $absentToday = $todayAttendances->where('status', 'absent')->count();

        // Fees
        $feesThisMonth = HostelFee::where('school_id', $schoolId)
            ->where(function ($q) use ($thisMonth) {
                $q->where('month', $thisMonth)
                  ->orWhere('created_at', 'like', "{$thisMonth}%");
            })
            ->get();

        $totalFeeAmount = $feesThisMonth->sum('amount');
        $totalFeeCollected = $feesThisMonth->sum('paid_amount');
        $pendingFees = max(0, $totalFeeAmount - $totalFeeCollected);

        // Complaints
        $pendingComplaints = HostelComplaint::where('school_id', $schoolId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        // Recent Allocations
        $recentAllocations = HostelRoomAllocation::with([
            'student:id,admission_no,first_name,last_name',
            'room:id,room_no',
            'bed:id,bed_no',
        ])
            ->where('school_id', $schoolId)
            ->latest('id')
            ->limit(5)
            ->get();

        // Recent Visitors
        $recentVisitors = HostelVisitor::with([
            'student:id,admission_no,first_name,last_name'
        ])
            ->where('school_id', $schoolId)
            ->latest('id')
            ->limit(5)
            ->get();

        // Recent Complaints
        $recentComplaints = HostelComplaint::with([
            'student:id,admission_no,first_name,last_name',
            'room:id,room_no',
        ])
            ->where('school_id', $schoolId)
            ->latest('id')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'counts' => [
                    'hostels' => $totalHostels,
                    'buildings' => $totalBuildings,
                    'rooms' => $totalRooms,
                    'total_capacity' => $effectiveCapacity,
                    'occupied_beds' => $effectiveOccupied,
                    'available_beds' => $availableBeds,
                    'occupancy_rate' => $occupancyRate,
                    'students' => $totalStudents,
                    'pending_complaints' => $pendingComplaints,
                ],
                'attendance' => [
                    'present' => $presentToday,
                    'absent' => $absentToday,
                    'total_marked' => $todayAttendances->count(),
                ],
                'fees' => [
                    'total_billed' => $totalFeeAmount,
                    'total_collected' => $totalFeeCollected,
                    'pending' => $pendingFees,
                ],
                'recent_allocations' => $recentAllocations,
                'recent_visitors' => $recentVisitors,
                'recent_complaints' => $recentComplaints,
            ],
        ]);
    }
}
