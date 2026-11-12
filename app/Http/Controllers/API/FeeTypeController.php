<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use App\Models\School;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function index(Request $request)
    {
        $isSuperAdmin = $this->isSuperAdmin($request);
        $schoolId = $this->resolveSchoolId($request);

        $query = FeeType::query()->with('school:id,name,code');

        if ($request->input('school_id') === 'all') {
            // Global: All schools
        } elseif ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        } elseif (!$isSuperAdmin && auth()->user()?->school_id) {
            $query->where('school_id', auth()->user()->school_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $data = $query
            ->latest('id')
            ->paginate($request->get('per_page', 20));

        $response = [
            'success' => true,
            'data' => $data,
        ];

        if ($isSuperAdmin) {
            $response['all_schools'] = School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']);
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'code' => [
                'nullable',
                'string',
                'max:50'
            ],
            'frequency' => [
                'required',
                'in:One Time,Monthly,Quarterly,Half Yearly,Yearly'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'status' => [
                'boolean'
            ],
        ]);

        $schoolId = $this->resolveSchoolId($request) ?? auth()->user()?->school_id;
        if (!$schoolId) {
            $schoolId = School::first()?->id ?? 1;
        }

        $feeType = FeeType::create([
            'school_id' => $schoolId,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'frequency' => $validated['frequency'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? true,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fee type created successfully.',
            'data' => $feeType,
        ], 201);
    }

    public function show(int $id)
    {
        $feeType = $this->getFeeType($id);

        return response()->json([
            'success' => true,
            'data' => $feeType,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $feeType = $this->getFeeType($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'code' => [
                'nullable',
                'string',
                'max:50'
            ],
            'frequency' => [
                'required',
                'in:One Time,Monthly,Quarterly,Half Yearly,Yearly'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'status' => [
                'boolean'
            ],
        ]);

        $feeType->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'frequency' => $validated['frequency'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? true,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fee type updated successfully.',
            'data' => $feeType->fresh(),
        ]);
    }

    public function destroy(int $id)
    {
        $feeType = $this->getFeeType($id);
        $feeType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee type deleted successfully.',
        ]);
    }

    protected function getFeeType(int $id): FeeType
    {
        $query = FeeType::query()->with('school:id,name,code');

        if (!$this->isSuperAdmin() && auth()->user()?->school_id) {
            $query->where('school_id', auth()->user()->school_id);
        }

        return $query->findOrFail($id);
    }
}
