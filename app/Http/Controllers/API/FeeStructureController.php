<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\School;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $isSuperAdmin = $this->isSuperAdmin($request);
        $schoolId = $this->resolveSchoolId($request);

        $query = FeeStructure::query()
            ->with([
                'academicSession',
                'class',
                'feeType',
                'school:id,name,code',
            ]);

        if ($request->input('school_id') === 'all') {
            // Global: All schools
        } elseif ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        } elseif (!$isSuperAdmin && auth()->user()?->school_id) {
            $query->where('school_id', auth()->user()->school_id);
        }

        if ($request->filled('academic_session_id')) {
            $query->where(
                'academic_session_id',
                $request->academic_session_id
            );
        }

        if ($request->filled('class_id')) {
            $query->where(
                'class_id',
                $request->class_id
            );
        }

        if ($request->filled('fee_type_id')) {
            $query->where(
                'fee_type_id',
                $request->fee_type_id
            );
        }

        if ($request->has('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $data = $query
            ->latest('id')
            ->paginate(
                $request->get('per_page', 20)
            );

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
            'academic_session_id' => [
                'required',
                'integer',
                'exists:academic_sessions,id',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],
            'fee_type_id' => [
                'required',
                'integer',
                'exists:fee_types,id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'effective_from' => [
                'nullable',
                'date',
            ],
            'effective_to' => [
                'nullable',
                'date',
            ],
            'due_day' => [
                'nullable',
                'integer',
                'min:1',
                'max:31',
            ],
            'late_fee' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        $schoolId = $this->resolveSchoolId($request) ?? auth()->user()?->school_id;
        if (!$schoolId) {
            $schoolId = School::first()?->id ?? 1;
        }

        $feeTypeQuery = \App\Models\FeeType::query()
            ->where('id', $validated['fee_type_id']);
        if ($schoolId && !$this->isSuperAdmin($request)) {
            $feeTypeQuery->where('school_id', $schoolId);
        }
        $feeTypeExists = $feeTypeQuery->exists();

        if (!$feeTypeExists) {
            return response()->json([
                'message' => 'Invalid fee type.',
            ], 422);
        }

        $feeStructure = FeeStructure::create([
            'school_id' => $schoolId,
            'academic_session_id' => $validated['academic_session_id'],
            'class_id' => $validated['class_id'],
            'fee_type_id' => $validated['fee_type_id'],
            'amount' => $validated['amount'],
            'effective_from' => $validated['effective_from'] ?? null,
            'effective_to' => $validated['effective_to'] ?? null,
            'due_day' => $validated['due_day'] ?? null,
            'late_fee' => $validated['late_fee'] ?? 0,
            'status' => $validated['status'] ?? true,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fee structure created successfully.',
            'data' => $feeStructure->load([
                'academicSession',
                'class',
                'feeType',
                'school:id,name,code',
            ]),
        ], 201);
    }

    public function show(int $id)
    {
        $feeStructure = $this->getFeeStructure($id);

        return response()->json([
            'success' => true,
            'data' => $feeStructure,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $feeStructure = $this->getFeeStructure($id);

        $validated = $request->validate([
            'academic_session_id' => [
                'required',
                'integer',
                'exists:academic_sessions,id',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],
            'fee_type_id' => [
                'required',
                'integer',
                'exists:fee_types,id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'effective_from' => [
                'nullable',
                'date',
            ],
            'effective_to' => [
                'nullable',
                'date',
            ],
            'due_day' => [
                'nullable',
                'integer',
                'min:1',
                'max:31',
            ],
            'late_fee' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        $schoolId = $feeStructure->school_id;

        $feeTypeQuery = \App\Models\FeeType::query()
            ->where('id', $validated['fee_type_id']);
        if ($schoolId && !$this->isSuperAdmin($request)) {
            $feeTypeQuery->where('school_id', $schoolId);
        }
        $feeTypeExists = $feeTypeQuery->exists();

        if (!$feeTypeExists) {
            return response()->json([
                'message' => 'Invalid fee type.',
            ], 422);
        }

        $feeStructure->update([
            'academic_session_id' => $validated['academic_session_id'],
            'class_id' => $validated['class_id'],
            'fee_type_id' => $validated['fee_type_id'],
            'amount' => $validated['amount'],
            'effective_from' => $validated['effective_from'] ?? null,
            'effective_to' => $validated['effective_to'] ?? null,
            'due_day' => $validated['due_day'] ?? null,
            'late_fee' => $validated['late_fee'] ?? 0,
            'status' => $validated['status'] ?? true,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fee structure updated successfully.',
            'data' => $feeStructure->fresh()->load([
                'academicSession',
                'class',
                'feeType',
                'school:id,name,code',
            ]),
        ]);
    }

    public function destroy(int $id)
    {
        $feeStructure = $this->getFeeStructure($id);
        $feeStructure->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee structure deleted successfully.',
        ]);
    }

    protected function getFeeStructure(int $id): FeeStructure
    {
        $query = FeeStructure::query()->with([
            'academicSession',
            'class',
            'feeType',
            'school:id,name,code',
        ]);

        if (!$this->isSuperAdmin() && auth()->user()?->school_id) {
            $query->where('school_id', auth()->user()->school_id);
        }

        return $query->findOrFail($id);
    }
}
