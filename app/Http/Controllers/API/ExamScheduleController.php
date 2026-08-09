<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamSchedule\StoreExamScheduleRequest;
use App\Http\Requests\ExamSchedule\UpdateExamScheduleRequest;
use App\Http\Resources\ExamScheduleResource;
use App\Models\ExamSchedule;
use App\Services\ExamScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamScheduleController extends Controller
{
    protected ExamScheduleService $service;

    public function __construct(ExamScheduleService $service)
    {
        $this->service = $service;
    }

    /**
     * List
     */
    public function index(Request $request)
    {
        return ExamScheduleResource::collection(
            $this->service->paginate($request->all())
        );
    }

    /**
     * Store
     */
    public function store(StoreExamScheduleRequest $request): JsonResponse
    {
        $schedule = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam Schedule created successfully.',
            'data'    => new ExamScheduleResource($schedule),
        ], 201);
    }

    /**
     * Show
     */
    public function show(ExamSchedule $examSchedule): ExamScheduleResource
    {
        return new ExamScheduleResource(
            $this->service->find($examSchedule->id)
        );
    }

    /**
     * Update
     */
    public function update(
        UpdateExamScheduleRequest $request,
        ExamSchedule $examSchedule
    ): JsonResponse {

        $schedule = $this->service->update(
            $examSchedule,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam Schedule updated successfully.',
            'data'    => new ExamScheduleResource($schedule),
        ]);
    }

    /**
     * Delete
     */
    public function destroy(ExamSchedule $examSchedule): JsonResponse
    {
        $this->service->delete($examSchedule);

        return response()->json([
            'success' => true,
            'message' => 'Exam Schedule deleted successfully.',
        ]);
    }

    /**
     * Active Schedules
     */
    public function active(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ExamScheduleResource::collection(
                $this->service->active()
            ),
        ]);
    }
}