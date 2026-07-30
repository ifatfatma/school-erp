<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreStudentRequest;
use App\Services\StudentService;

class StudentController extends BaseController
{
    public function __construct(
        private StudentService $service
    ) {}

    public function index()
    {
        return $this->success(
            $this->service->paginate(),
            'Students fetched successfully.'
        );
    }

    public function store(StoreStudentRequest $request)
    {
        $student = $this->service->create(
            $request->validated()
        );

        return $this->success(
            $student,
            'Student created successfully.',
            201
        );
    }

    public function show($id)
    {
        return $this->success(
            $this->service->find($id)
        );
    }

    public function update(
        StoreStudentRequest $request,
        $id
    ) {
        return $this->success(
            $this->service->update(
                $id,
                $request->validated()
            ),
            'Student updated successfully.'
        );
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return $this->success(
            null,
            'Student deleted successfully.'
        );
    }
}