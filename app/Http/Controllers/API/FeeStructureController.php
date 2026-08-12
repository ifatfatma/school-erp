<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeStructure::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->with([
                'academicSession',
                'class',
                'feeType',
            ]);

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

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
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
                'after_or_equal:effective_from',
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
                'boolean',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Validate Fee Type belongs to this school
        |--------------------------------------------------------------------------
        */

        $feeTypeExists = \App\Models\FeeType::query()
            ->where(
                'id',
                $validated['fee_type_id']
            )
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->exists();

        if (!$feeTypeExists) {

            return response()->json([
                'message' =>
                    'Invalid fee type.',
            ], 422);
        }


        $feeStructure = FeeStructure::create([

            'school_id' =>
                auth()->user()->school_id,

            'academic_session_id' =>
                $validated['academic_session_id'],

            'class_id' =>
                $validated['class_id'],

            'fee_type_id' =>
                $validated['fee_type_id'],

            'amount' =>
                $validated['amount'],

            'effective_from' =>
                $validated['effective_from'] ?? null,

            'effective_to' =>
                $validated['effective_to'] ?? null,

            'due_day' =>
                $validated['due_day'] ?? null,

            'late_fee' =>
                $validated['late_fee'] ?? 0,

            'status' =>
                $validated['status'] ?? true,

            'created_by' =>
                auth()->id(),

        ]);


        return response()->json([

            'success' => true,

            'message' =>
                'Fee structure created successfully.',

            'data' =>
                $feeStructure->load([
                    'academicSession',
                    'class',
                    'feeType',
                ]),

        ], 201);
    }


    public function show(int $id)
    {
        $feeStructure = FeeStructure::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->with([
                'academicSession',
                'class',
                'feeType',
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $feeStructure,
        ]);
    }


    public function update(
        Request $request,
        int $id
    ) {

        $feeStructure = FeeStructure::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->findOrFail($id);


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
                'after_or_equal:effective_from',
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
                'boolean',
            ],

        ]);


        $feeTypeExists = \App\Models\FeeType::query()
            ->where(
                'id',
                $validated['fee_type_id']
            )
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->exists();

        if (!$feeTypeExists) {

            return response()->json([
                'message' =>
                    'Invalid fee type.',
            ], 422);
        }


        $feeStructure->update([

            'academic_session_id' =>
                $validated['academic_session_id'],

            'class_id' =>
                $validated['class_id'],

            'fee_type_id' =>
                $validated['fee_type_id'],

            'amount' =>
                $validated['amount'],

            'effective_from' =>
                $validated['effective_from'] ?? null,

            'effective_to' =>
                $validated['effective_to'] ?? null,

            'due_day' =>
                $validated['due_day'] ?? null,

            'late_fee' =>
                $validated['late_fee'] ?? 0,

            'status' =>
                $validated['status'] ?? true,

            'updated_by' =>
                auth()->id(),

        ]);


        return response()->json([

            'success' => true,

            'message' =>
                'Fee structure updated successfully.',

            'data' =>
                $feeStructure->fresh()->load([
                    'academicSession',
                    'class',
                    'feeType',
                ]),

        ]);
    }


    public function destroy(int $id)
    {
        $feeStructure = FeeStructure::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->findOrFail($id);


        $feeStructure->delete();


        return response()->json([

            'success' => true,

            'message' =>
                'Fee structure deleted successfully.',

        ]);
    }
}