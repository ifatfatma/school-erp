<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected StudentService $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;

        /*
        $this->middleware('permission:student-list')->only(['index','show']);
        $this->middleware('permission:student-create')->only(['store']);
        $this->middleware('permission:student-edit')->only(['update']);
        $this->middleware('permission:student-delete')->only(['destroy']);
        */
    }

    /**
     * Student List
     */
    public function index(Request $request)
    {
        $students = $this->service->paginate([
            'search'     => $request->search,
            'class_id'   => $request->class_id,
            'section_id' => $request->section_id,
            'status'     => $request->status,
            'per_page'   => $request->per_page ?? 15,
        ]);

        return StudentResource::collection($students);
    }

    /**
     * Store Student
     */
    public function store(StoreStudentRequest $request)
    {
        $student = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully.',
            'data'    => new StudentResource($student),
        ], 201);
    }

    /**
     * Show Student
     */
    public function show(Student $student)
    {
        $student = $this->service->find($student->id);

        return new StudentResource($student);
    }

    /**
     * Update Student
     */
    public function update(
        UpdateStudentRequest $request,
        Student $student
    ) {
        $student = $this->service->update(
            $student,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data'    => new StudentResource($student),
        ]);
    }

    /**
     * Delete Student
     */
    public function destroy(Student $student)
    {
        $this->service->delete($student);

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.',
        ]);
    }

    /**
     * Active Students
     */
    public function active()
    {
        return StudentResource::collection(
            $this->service->active()
        );
    }

    /**
     * Students by Class
     */
    public function byClass(Request $request)
    {
        $students = $this->service->byClass(
            $request->class_id,
            $request->section_id
        );

        return StudentResource::collection($students);
    }
}