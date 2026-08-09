<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamSubject\StoreExamSubjectRequest;
use App\Http\Requests\ExamSubject\UpdateExamSubjectRequest;
use App\Http\Resources\ExamSubjectResource;
use App\Models\ExamSubject;
use App\Services\ExamSubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamSubjectController extends Controller
{
    protected ExamSubjectService $service;

    public function __construct(ExamSubjectService $service)
    {
        $this->service = $service;
    }

    /**
     * List
     */
    public function index(Request $request)
    {
        return ExamSubjectResource::collection(
            $this->service->paginate($request->all())
        );
    }

    /**
     * Active List
     */
    public function active(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ExamSubjectResource::collection(
                $this->service->active()
            )
        ]);
    }

    /**
     * Subjects By Exam & Class
     */
    public function byExamAndClass(
        int $examId,
        int $classId
    ): JsonResponse {

        return response()->json([
            'success' => true,
            'data' => ExamSubjectResource::collection(
                $this->service->byExamAndClass(
                    $examId,
                    $classId
                )
            )
        ]);
    }

    /**
     * Store
     */
    public function store(StoreExamSubjectRequest $request): JsonResponse
    {
        $item = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam Subject created successfully.',
            'data' => new ExamSubjectResource($item),
        ], 201);
    }

    /**
     * Show
     */
    public function show(ExamSubject $examSubject): ExamSubjectResource
    {
        return new ExamSubjectResource(
            $this->service->find($examSubject->id)
        );
    }

    /**
     * Update
     */
    public function update(
        UpdateExamSubjectRequest $request,
        ExamSubject $examSubject
    ): JsonResponse {

        $item = $this->service->update(
            $examSubject,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam Subject updated successfully.',
            'data' => new ExamSubjectResource($item),
        ]);
    }

    /**
     * Delete
     */
    public function destroy(
        ExamSubject $examSubject
    ): JsonResponse {

        $this->service->delete($examSubject);

        return response()->json([
            'success' => true,
            'message' => 'Exam Subject deleted successfully.',
        ]);
    }

   
}