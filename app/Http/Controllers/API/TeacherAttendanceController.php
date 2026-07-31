<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherAttendanceRequest;
use App\Http\Requests\UpdateTeacherAttendanceRequest;
use App\Http\Resources\TeacherAttendanceResource;
use App\Models\TeacherAttendance;
use App\Services\TeacherAttendanceService;
use Illuminate\Http\Request;

class TeacherAttendanceController extends Controller
{
    protected TeacherAttendanceService $service;

    public function __construct(TeacherAttendanceService $service)
    {
        $this->service = $service;

        // Uncomment if using permissions
        // $this->middleware('permission:teacher-attendance-list')->only(['index','show']);
        // $this->middleware('permission:teacher-attendance-create')->only(['store']);
        // $this->middleware('permission:teacher-attendance-edit')->only(['update']);
        // $this->middleware('permission:teacher-attendance-delete')->only(['destroy']);
    }

    /**
     * Display a listing.
     */
    public function index(Request $request)
    {
        $filters = [
            'teacher_id'      => $request->teacher_id,
            'attendance_date' => $request->attendance_date,
            'status'          => $request->status,
            'per_page'        => $request->per_page ?? 15,
        ];

        $records = $this->service->paginate($filters);

        return TeacherAttendanceResource::collection($records);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreTeacherAttendanceRequest $request)
    {
        $attendance = $this->service->store(
            $request->validated()
        );

        return new TeacherAttendanceResource($attendance);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherAttendance $teacherAttendance)
    {
        return new TeacherAttendanceResource(
            $this->service->find($teacherAttendance->id)
        );
    }

    /**
     * Update the specified resource.
     */
    public function update(
        UpdateTeacherAttendanceRequest $request,
        TeacherAttendance $teacherAttendance
    ) {
        $attendance = $this->service->update(
            $teacherAttendance,
            $request->validated()
        );

        return new TeacherAttendanceResource($attendance);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(TeacherAttendance $teacherAttendance)
    {
        $this->service->delete($teacherAttendance);

        return response()->json([
            'success' => true,
            'message' => 'Teacher attendance deleted successfully.'
        ]);
    }
}