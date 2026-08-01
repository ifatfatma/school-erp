<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guardian\StoreGuardianRequest;
use App\Http\Requests\Guardian\UpdateGuardianRequest;
use App\Http\Resources\GuardianResource;
use App\Models\Guardian;
use App\Services\GuardianService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    protected GuardianService $service;

    public function __construct(GuardianService $service)
    {
        $this->service = $service;
    }

    /**
     * Guardian List
     */
    public function index(Request $request)
    {
        $guardians = $this->service->paginate(
            $request->all()
        );

        return GuardianResource::collection($guardians);
    }

    /**
     * Store Guardian
     */
    public function store(StoreGuardianRequest $request): JsonResponse
    {
        $guardian = $this->service->store(
            $request->validated()
        );

        return response()->json([
            'message' => 'Guardian created successfully.',
            'data'    => new GuardianResource($guardian),
        ], 201);
    }

    /**
     * Show Guardian
     */
    public function show(Guardian $guardian): GuardianResource
    {
        $guardian = $this->service->find($guardian->id);

        return new GuardianResource($guardian);
    }

    /**
     * Update Guardian
     */
    public function update(
        UpdateGuardianRequest $request,
        Guardian $guardian
    ): JsonResponse {

        $guardian = $this->service->update(
            $guardian,
            $request->validated()
        );

        return response()->json([
            'message' => 'Guardian updated successfully.',
            'data'    => new GuardianResource($guardian),
        ]);
    }

    /**
     * Delete Guardian
     */
    public function destroy(Guardian $guardian): JsonResponse
    {
        $this->service->delete($guardian);

        return response()->json([
            'message' => 'Guardian deleted successfully.',
        ]);
    }
}