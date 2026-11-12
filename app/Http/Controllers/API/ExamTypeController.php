<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamType\StoreExamTypeRequest;
use App\Http\Requests\ExamType\UpdateExamTypeRequest;
use App\Http\Resources\ExamTypeResource;
use App\Models\ExamType;
use App\Models\School;
use App\Services\ExamTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    protected ExamTypeService $service;

    public function __construct(ExamTypeService $service)
    {
        $this->service = $service;
    }

    /**
     * List
     */
    public function index(Request $request)
    {
        $isSuperAdmin = $this->isSuperAdmin($request);

        $examTypes = $this->service->paginate(
            $request->all()
        );

        $resource = ExamTypeResource::collection($examTypes);

        if ($isSuperAdmin) {
            $resource->additional([
                'all_schools' => School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']),
            ]);
        }

        return $resource;
    }

    /**
     * Store
     */
    public function store(StoreExamTypeRequest $request): JsonResponse
    {
        $examType = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam Type created successfully.',
            'data' => new ExamTypeResource($examType),
        ], 201);
    }

    /**
     * Show
     */
    public function show(ExamType $examType): ExamTypeResource
    {
        return new ExamTypeResource(
            $this->service->find($examType->id)
        );
    }

    /**
     * Update
     */
    public function update(
        UpdateExamTypeRequest $request,
        ExamType $examType
    ): JsonResponse {

        $examType = $this->service->update(
            $examType,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam Type updated successfully.',
            'data' => new ExamTypeResource($examType),
        ]);
    }

    /**
     * Delete
     */
    public function destroy(ExamType $examType): JsonResponse
    {
        $this->service->delete($examType);

        return response()->json([
            'success' => true,
            'message' => 'Exam Type deleted successfully.',
        ]);
    }

    /**
     * Active Exam Types
     */
    public function active(Request $request): JsonResponse
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;

        return response()->json([
            'success' => true,
            'data' => ExamTypeResource::collection(
                $this->service->active($schoolId)
            ),
        ]);
    }
}
