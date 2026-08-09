<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Promotion\StoreStudentPromotionRequest;
use App\Http\Resources\StudentPromotionResource;
use App\Models\StudentPromotion;
use App\Services\StudentPromotionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentPromotionController extends Controller
{
    protected StudentPromotionService $service;

    public function __construct(StudentPromotionService $service)
    {
        $this->service = $service;
    }

    /**
     * Promotion List
     */
    public function index(Request $request)
    {
        $promotions = $this->service->paginate(
            $request->all()
        );

        return StudentPromotionResource::collection($promotions);
    }

    /**
     * Single Promotion
     */
    public function store(StoreStudentPromotionRequest $request): JsonResponse
    {
        $promotion = $this->service->promote(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Student promoted successfully.',
            'data'    => new StudentPromotionResource($promotion),
        ], 201);
    }

    /**
     * Bulk Promotion
     */
    public function bulkPromotion(StoreStudentPromotionRequest $request): JsonResponse
    {
        $this->service->bulkPromote(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Students promoted successfully.',
        ]);
    }

    /**
     * Promotion Details
     */
    public function show(StudentPromotion $studentPromotion): StudentPromotionResource
    {
        return new StudentPromotionResource(
            $this->service->find($studentPromotion->id)
        );
    }

    /**
     * Delete Promotion History
     */
    public function destroy(StudentPromotion $studentPromotion): JsonResponse
    {
        $this->service->delete($studentPromotion);

        return response()->json([
            'success' => true,
            'message' => 'Promotion record deleted successfully.',
        ]);
    }
}