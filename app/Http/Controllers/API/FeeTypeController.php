<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeType::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            );

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                );

            });
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


        $feeType = FeeType::create([

            'school_id' =>
                auth()->user()->school_id,

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?? null,

            'frequency' =>
                $validated['frequency'],

            'description' =>
                $validated['description'] ?? null,

            'status' =>
                $validated['status'] ?? true,

            'created_by' =>
                auth()->id(),

        ]);


        return response()->json([

            'success' => true,

            'message' =>
                'Fee type created successfully.',

            'data' =>
                $feeType,

        ], 201);
    }


    public function show(int $id)
    {
        $feeType = FeeType::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $feeType,
        ]);
    }


    public function update(
        Request $request,
        int $id
    ) {

        $feeType = FeeType::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->findOrFail($id);


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

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?? null,

            'frequency' =>
                $validated['frequency'],

            'description' =>
                $validated['description'] ?? null,

            'status' =>
                $validated['status'] ?? true,

            'updated_by' =>
                auth()->id(),

        ]);


        return response()->json([

            'success' => true,

            'message' =>
                'Fee type updated successfully.',

            'data' =>
                $feeType->fresh(),

        ]);
    }


    public function destroy(int $id)
    {
        $feeType = FeeType::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->findOrFail($id);


        $feeType->delete();


        return response()->json([

            'success' => true,

            'message' =>
                'Fee type deleted successfully.',

        ]);
    }
}