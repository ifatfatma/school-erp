<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelBuilding;
use App\Models\HostelFloor;
use Illuminate\Http\Request;

class HostelFloorController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $query = HostelFloor::query()
            ->with(['building:id,name,code'])
            ->where('school_id', $this->schoolId());

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('building', function ($b) use ($search) {
                      $b->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('hostel_building_id')) {
            $query->where('hostel_building_id', $request->hostel_building_id);
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $perPage = (int) $request->input('per_page', 15);
        $floors = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $floors->items(),
            'meta' => [
                'current_page' => $floors->currentPage(),
                'last_page' => $floors->lastPage(),
                'per_page' => $floors->perPage(),
                'total' => $floors->total(),
            ],
        ]);
    }

    public function buildings()
    {
        $buildings = HostelBuilding::where('school_id', $this->schoolId())
            ->where('status', true)
            ->select('id', 'name', 'code', 'total_floors')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $buildings,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostel_building_id' => 'required|exists:hostel_buildings,id',
            'floor_no' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validated['school_id'] = $this->schoolId();
        $validated['status'] = $request->input('status', true);

        $floor = HostelFloor::create($validated);
        $floor->load('building:id,name,code');

        return response()->json([
            'success' => true,
            'message' => 'Hostel floor created successfully.',
            'data' => $floor,
        ], 201);
    }

    public function show(int $id)
    {
        $floor = HostelFloor::with('building:id,name,code')
            ->where('school_id', $this->schoolId())
            ->find($id);

        if (!$floor) {
            return response()->json([
                'success' => false,
                'message' => 'Floor not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $floor,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $floor = HostelFloor::where('school_id', $this->schoolId())->find($id);

        if (!$floor) {
            return response()->json([
                'success' => false,
                'message' => 'Floor not found.',
            ], 404);
        }

        $validated = $request->validate([
            'hostel_building_id' => 'required|exists:hostel_buildings,id',
            'floor_no' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $floor->update($validated);
        $floor->load('building:id,name,code');

        return response()->json([
            'success' => true,
            'message' => 'Floor updated successfully.',
            'data' => $floor,
        ]);
    }

    public function toggleStatus(int $id)
    {
        $floor = HostelFloor::where('school_id', $this->schoolId())->find($id);

        if (!$floor) {
            return response()->json([
                'success' => false,
                'message' => 'Floor not found.',
            ], 404);
        }

        $floor->status = !$floor->status;
        $floor->save();

        return response()->json([
            'success' => true,
            'message' => 'Floor status updated successfully.',
            'data' => $floor,
        ]);
    }

    public function destroy(int $id)
    {
        $floor = HostelFloor::where('school_id', $this->schoolId())->find($id);

        if (!$floor) {
            return response()->json([
                'success' => false,
                'message' => 'Floor not found.',
            ], 404);
        }

        $floor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Floor deleted successfully.',
        ]);
    }
}
