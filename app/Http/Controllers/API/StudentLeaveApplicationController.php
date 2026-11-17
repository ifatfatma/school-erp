<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentAttendance;
use App\Models\StudentLeaveApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLeaveApplicationController extends Controller
{
    /**
     * Display a listing of student leave applications for admin review.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        $query = StudentLeaveApplication::with([
            'student:id,school_id,class_id,section_id,first_name,last_name,admission_no,roll_no,photo,gender',
            'student.class:id,name',
            'student.section:id,name',
            'guardian:id,user_id,first_name,last_name,mobile,email,relation',
            'reviewer:id,name,email',
            'school:id,name,code',
        ]);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by student name, admission_no, or reason
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('leave_type', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('admission_no', 'like', "%{$search}%");
                  });
            });
        }

        // Calculate summary counters
        $statsQuery = StudentLeaveApplication::query();
        if ($schoolId) {
            $statsQuery->where('school_id', $schoolId);
        }

        $totalCount = (clone $statsQuery)->count();
        $pendingCount = (clone $statsQuery)->where('status', 'Pending')->count();
        $approvedCount = (clone $statsQuery)->where('status', 'Approved')->count();
        $rejectedCount = (clone $statsQuery)->where('status', 'Rejected')->count();

        $perPage = (int) $request->get('per_page', 15);
        $applications = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $applications,
            'summary' => [
                'total' => $totalCount,
                'pending' => $pendingCount,
                'approved' => $approvedCount,
                'rejected' => $rejectedCount,
            ],
        ]);
    }

    /**
     * Approve student leave application
     */
    public function approve(Request $request, $id)
    {
        $application = StudentLeaveApplication::with('student')->findOrFail($id);

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status' => 'Approved',
            'reviewed_by' => Auth::id(),
            'remarks' => $request->remarks ?: 'Leave sanctioned by administration',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student leave application has been approved successfully.',
            'data' => $application,
        ]);
    }

    /**
     * Reject student leave application
     */
    public function reject(Request $request, $id)
    {
        $application = StudentLeaveApplication::with('student')->findOrFail($id);

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status' => 'Rejected',
            'reviewed_by' => Auth::id(),
            'remarks' => $request->remarks ?: 'Leave request declined by administration',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student leave application has been rejected.',
            'data' => $application,
        ]);
    }

    /**
     * Delete a student leave application record
     */
    public function destroy($id)
    {
        $application = StudentLeaveApplication::findOrFail($id);
        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student leave application deleted successfully.',
        ]);
    }
}
