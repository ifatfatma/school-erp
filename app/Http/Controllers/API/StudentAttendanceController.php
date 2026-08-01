<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentAttendance\StoreStudentAttendanceRequest;
use App\Http\Requests\StudentAttendance\UpdateStudentAttendanceRequest;
use App\Http\Requests\StudentAttendance\BulkStudentAttendanceRequest;
use App\Http\Resources\StudentAttendanceResource;
use App\Models\StudentAttendance;
use App\Services\StudentAttendanceService;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    protected StudentAttendanceService $service;

    public function __construct(StudentAttendanceService $service)
    {
        $this->service = $service;
    }

    /**
     * Attendance List
     */
    public function index(Request $request)
    {
        $attendance = $this->service->paginate(
            $request->all()
        );

        return StudentAttendanceResource::collection($attendance);
    }

    /**
     * Store Attendance
     */
    public function store(StoreStudentAttendanceRequest $request)
    {
        $attendance = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'message' => 'Attendance created successfully.',
            'data' => new StudentAttendanceResource($attendance),
        ], 201);
    }

    /**
     * Show Attendance
     */
    public function show(StudentAttendance $studentAttendance)
    {
        $attendance = $this->service->find(
            $studentAttendance->id
        );

        return new StudentAttendanceResource($attendance);
    }

    /**
     * Update Attendance
     */
    public function update(
        UpdateStudentAttendanceRequest $request,
        StudentAttendance $studentAttendance
    ) {
        $attendance = $this->service->update(
            $studentAttendance,
            $request->validated()
        );

        return response()->json([
            'message' => 'Attendance updated successfully.',
            'data' => new StudentAttendanceResource($attendance),
        ]);
    }

    /**
     * Delete Attendance
     */
    public function destroy(StudentAttendance $studentAttendance)
    {
        $this->service->delete($studentAttendance);

        return response()->json([
            'message' => 'Attendance deleted successfully.',
        ]);
    }

    /**
     * Bulk Attendance Save
     */
    public function bulkStore(BulkStudentAttendanceRequest $request)
    {
        $records = [];

        foreach ($request->records as $row) {

            $records[] = [

                'school_id' => auth()->user()->school_id,

                'student_id' => $row['student_id'],

                'class_id' => $request->class_id,

                'section_id' => $request->section_id,

                'attendance_date' => $request->attendance_date,

                'status' => $row['status'],

                'remarks' => $row['remarks'] ?? null,

                'check_in' => $row['check_in'] ?? null,

                'check_out' => $row['check_out'] ?? null,

                'created_by' => auth()->id(),

                'updated_by' => auth()->id(),

            ];
        }

        $this->service->bulkStore($records);

        return response()->json([
            'message' => 'Attendance saved successfully.',
        ]);
    }

    /**
     * Monthly Attendance
     */
    public function monthly(Request $request)
    {
        $data = $this->service->monthly(
            $request->student_id,
            $request->month,
            $request->year
        );

        return StudentAttendanceResource::collection($data);
    }

    /**
     * Attendance Summary
     */
    public function summary(Request $request)
    {
        return response()->json(
            $this->service->summary(
                $request->student_id,
                $request->month,
                $request->year
            )
        );
    }
}