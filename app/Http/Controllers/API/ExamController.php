<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\StoreExamRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    protected ExamService $service;

    public function __construct(ExamService $service)
    {
        $this->service = $service;
    }

    /**
     * List
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user && (
            $user->user_type === 'super_admin' || 
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))) ||
            ($user->roles && $user->roles->contains('name', 'Super Admin'))
        );

        $resource = ExamResource::collection(
            $this->service->paginate($request->all())
        );

        if ($isSuperAdmin) {
            $resource->additional([
                'all_schools' => \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']),
            ]);
        }

        return $resource;
    }

    /**
     * Store
     */
    public function store(StoreExamRequest $request): JsonResponse
    {
        $exam = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam created successfully.',
            'data' => new ExamResource($exam),
        ], 201);
    }

    /**
     * Show
     */
    public function show(Exam $exam): ExamResource
    {
        return new ExamResource(
            $this->service->find($exam->id)
        );
    }

    /**
     * Update
     */
    public function update(
        UpdateExamRequest $request,
        Exam $exam
    ): JsonResponse {

        $exam = $this->service->update(
            $exam,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam updated successfully.',
            'data' => new ExamResource($exam),
        ]);
    }

    /**
     * Delete
     */
    public function destroy(Exam $exam): JsonResponse
    {
        $this->service->delete($exam);

        return response()->json([
            'success' => true,
            'message' => 'Exam deleted successfully.',
        ]);
    }

    /**
     * Active Exams
     */
    public function active(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => ExamResource::collection(
                $this->service->active()
            ),
        ]);
    }

    /**
     * Lock Marks
     */
    public function lockMarks(Exam $exam): JsonResponse
    {
        $exam = $this->service->lockMarks($exam);

        return response()->json([
            'success' => true,
            'message' => 'Marks locked successfully.',
            'data' => new ExamResource($exam),
        ]);
    }

    /**
     * Unlock Marks
     */
    public function unlockMarks(Exam $exam): JsonResponse
    {
        $exam = $this->service->unlockMarks($exam);

        return response()->json([
            'success' => true,
            'message' => 'Marks unlocked successfully.',
            'data' => new ExamResource($exam),
        ]);
    }

    /**
     * Publish Result
     */
    public function publishResult(Exam $exam): JsonResponse
    {
        $exam = $this->service->publishResult($exam);

        return response()->json([
            'success' => true,
            'message' => 'Result published successfully.',
            'data' => new ExamResource($exam),
        ]);
    }

    /**
     * Unpublish Result
     */
    public function unpublishResult(Exam $exam): JsonResponse
    {
        $exam = $this->service->unpublishResult($exam);

        return response()->json([
            'success' => true,
            'message' => 'Result unpublished successfully.',
            'data' => new ExamResource($exam),
        ]);
    }
}