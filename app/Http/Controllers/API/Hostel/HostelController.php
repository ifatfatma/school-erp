<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    protected function isSuperAdmin(?Request $request = null): bool
    {
        $user = $request->user();
        return $user && (
            $user->user_type === 'super_admin' || 
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))) ||
            ($user->roles && $user->roles->contains('name', 'Super Admin'))
        );
    }

    protected function resolveSchoolId(?Request $request = null): ?int
    {
        $user = $request->user();
        if (!$this->isSuperAdmin($request) && $user && $user->school_id) {
            return (int) $user->school_id;
        }

        $req = $request->input('school_id');
        if ($req !== null && $req !== '' && $req !== 'all' && $req !== '0') {
            return (int) $req;
        }

        return null;
    }

    public function index(Request $request)
    {
        $isSuper = $this->isSuperAdmin($request);
        $schoolId = $this->resolveSchoolId($request);

        $query = Hostel::query()
            ->withCount(['buildings', 'students'])
            ->with('school:id,name,code');

        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('warden_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = (int) $request->input('per_page', 15);
        $hostels = $query->latest('id')->paginate($perPage);

        $allSchools = $isSuper 
            ? \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']) 
            : [];

        return response()->json([
            'success' => true,
            'data' => $hostels->items(),
            'all_schools' => $allSchools,
            'meta' => [
                'current_page' => $hostels->currentPage(),
                'last_page' => $hostels->lastPage(),
                'per_page' => $hostels->perPage(),
                'total' => $hostels->total(),
            ],
        ]);
    }

    public function all()
    {
        $hostels = Hostel::where('school_id', $this->schoolId())
            ->where('status', true)
            ->select('id', 'name', 'code', 'type')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $hostels,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|string|in:boys,girls,co-ed',
            'warden_name' => 'nullable|string|max:255',
            'warden_phone' => 'nullable|string|max:255',
            'warden_email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validated['school_id'] = $this->schoolId();
        $validated['status'] = $request->input('status', true);

        $hostel = Hostel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hostel created successfully.',
            'data' => $hostel,
        ], 201);
    }

    public function show(int $id)
    {
        $hostel = Hostel::with(['buildings'])->where('school_id', $this->schoolId())->find($id);

        if (!$hostel) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hostel,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $hostel = Hostel::where('school_id', $this->schoolId())->find($id);

        if (!$hostel) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|string|in:boys,girls,co-ed',
            'warden_name' => 'nullable|string|max:255',
            'warden_phone' => 'nullable|string|max:255',
            'warden_email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $hostel->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hostel updated successfully.',
            'data' => $hostel,
        ]);
    }

    public function toggleStatus(int $id)
    {
        $hostel = Hostel::where('school_id', $this->schoolId())->find($id);

        if (!$hostel) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel not found.',
            ], 404);
        }

        $hostel->status = !$hostel->status;
        $hostel->save();

        return response()->json([
            'success' => true,
            'message' => 'Hostel status updated successfully.',
            'data' => $hostel,
        ]);
    }

    public function destroy(int $id)
    {
        $hostel = Hostel::where('school_id', $this->schoolId())->find($id);

        if (!$hostel) {
            return response()->json([
                'success' => false,
                'message' => 'Hostel not found.',
            ], 404);
        }

        $hostel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hostel deleted successfully.',
        ]);
    }
}
