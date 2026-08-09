<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Services\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected TeacherService $service;

    public function __construct(TeacherService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing.
     */
    public function index(Request $request)
    {
        $teachers = $this->service->paginate($request->all());

        return TeacherResource::collection($teachers);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreTeacherRequest $request)
    {
        $teacher = $this->service->store($request->validated());

        return new TeacherResource($teacher);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $teacher = $this->service->find($id);

        abort_if(!$teacher, 404, 'Teacher not found.');

        return new TeacherResource($teacher);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateTeacherRequest $request, int $id)
    {
        $teacher = $this->service->update(
            $id,
            $request->validated()
        );

        return new TeacherResource($teacher);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully.'
        ]);
    }

     public function active()
{
    return response()->json([
        'success' => true,
        'data' => Teacher::where('status', 1)
            ->orderBy('first_name')
            ->get()
    ]);
}
}