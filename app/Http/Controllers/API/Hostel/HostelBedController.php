<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelBed;
use App\Models\HostelRoom;
use Illuminate\Http\Request;

class HostelBedController extends Controller
{
    protected function schoolId(): int
    {
        return (int) (auth('sanctum')->user()?->school_id ?? auth()->user()?->school_id ?? 1);
    }

    public function index(Request $request)
    {
        $query = HostelBed::query()
            ->with([
                'room:id,room_no,floor_no,room_type,hostel_building_id',
                'room.building:id,name,code',
                'allocation.student:id,admission_no,first_name,last_name'
            ])
            ->where('school_id', $this->schoolId());

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('bed_no', 'like', "%{$search}%")
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_no', 'like', "%{$search}%")
                         ->orWhereHas('building', function ($bq) use ($search) {
                             $bq->where('name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($request->filled('hostel_room_id')) {
            $query->where('hostel_room_id', $request->hostel_room_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->input('per_page', 20);
        $beds = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $beds->items(),
            'meta' => [
                'current_page' => $beds->currentPage(),
                'last_page' => $beds->lastPage(),
                'per_page' => $beds->perPage(),
                'total' => $beds->total(),
            ],
        ]);
    }

    public function rooms()
    {
        $rooms = HostelRoom::with('building:id,name,code')
            ->where('school_id', $this->schoolId())
            ->where('status', true)
            ->select('id', 'room_no', 'hostel_building_id', 'capacity', 'occupied_beds')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rooms,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostel_room_id' => 'required|exists:hostel_rooms,id',
            'bed_no' => 'required|string|max:50',
            'status' => 'required|string|in:available,occupied,maintenance',
            'monthly_fee' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $validated['school_id'] = $this->schoolId();

        $bed = HostelBed::create($validated);
        $bed->load(['room.building']);

        return response()->json([
            'success' => true,
            'message' => 'Hostel bed created successfully.',
            'data' => $bed,
        ], 201);
    }

    public function show(int $id)
    {
        $bed = HostelBed::with(['room.building', 'allocation.student'])
            ->where('school_id', $this->schoolId())
            ->find($id);

        if (!$bed) {
            return response()->json([
                'success' => false,
                'message' => 'Bed not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bed,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $bed = HostelBed::where('school_id', $this->schoolId())->find($id);

        if (!$bed) {
            return response()->json([
                'success' => false,
                'message' => 'Bed not found.',
            ], 404);
        }

        $validated = $request->validate([
            'hostel_room_id' => 'required|exists:hostel_rooms,id',
            'bed_no' => 'required|string|max:50',
            'status' => 'required|string|in:available,occupied,maintenance',
            'monthly_fee' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $bed->update($validated);
        $bed->load(['room.building']);

        return response()->json([
            'success' => true,
            'message' => 'Bed updated successfully.',
            'data' => $bed,
        ]);
    }

    public function destroy(int $id)
    {
        $bed = HostelBed::where('school_id', $this->schoolId())->find($id);

        if (!$bed) {
            return response()->json([
                'success' => false,
                'message' => 'Bed not found.',
            ], 404);
        }

        $bed->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bed deleted successfully.',
        ]);
    }
}
