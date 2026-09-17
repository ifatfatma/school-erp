<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hostel\HostelBuildingRequest;
use App\Http\Resources\Hostel\HostelBuildingResource;
use App\Models\HostelBuilding;
use App\Services\Hostel\HostelBuildingService;
use Illuminate\Http\Request;

class HostelBuildingController extends Controller
{
    public function __construct(
        protected HostelBuildingService $service
    ) {
    }

    public function index(Request $request)
    {
        $buildings = $this->service->getAll([
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'per_page' => $request->input('per_page', 15),
        ]);

        return response()->json([
            'success' => true,
            'data' => HostelBuildingResource::collection($buildings),
            'meta' => [
                'current_page' => $buildings->currentPage(),
                'last_page' => $buildings->lastPage(),
                'per_page' => $buildings->perPage(),
                'total' => $buildings->total(),
            ],
        ]);
    }

    public function store(HostelBuildingRequest $request)
    {
        $building = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Hostel building created successfully.',
            'data' => new HostelBuildingResource($building),
        ], 201);
    }

    public function show(int $id)
    {
        $building = $this->service->find($id);

        if (!$building) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel building not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new HostelBuildingResource($building),
        ]);
    }

    public function update(
        HostelBuildingRequest $request,
        int $id
    ) {
        $building = $this->service->find($id);

        if (!$building) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel building not found.',
            ], 404);
        }

        $building = $this->service->update(
            $building,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Hostel building updated successfully.',
            'data' => new HostelBuildingResource($building),
        ]);
    }

    public function destroy(int $id)
    {
        $building = $this->service->find($id);

        if (!$building) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel building not found.',
            ], 404);
        }

        $this->service->delete($building);

        return response()->json([
            'success' => true,
            'message' => 'Hostel building deleted successfully.',
        ]);
    }

    public function toggleStatus(int $id)
    {
        $building = $this->service->find($id);

        if (!$building) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel building not found.',
            ], 404);
        }

        $building->update([
            'status' => !$building->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Building status updated successfully.',
            'data' => new HostelBuildingResource($building->fresh()),
        ]);
    }
}