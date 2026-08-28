<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryUnitController extends Controller
{
    protected function schoolId()
    {
        return auth()->user()->school_id;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = InventoryUnit::query()
            ->where(
                'school_id',
                $schoolId
            );

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                );

                $q->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->has('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $units = $query
            ->latest()
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $units
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();

        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'description' => [
                    'nullable',
                    'string'
                ],

                'status' => [
                    'nullable',
                    'boolean'
                ],
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Name
        |--------------------------------------------------------------------------
        */

        $nameExists = InventoryUnit::where(
            'school_id',
            $schoolId
        )
        ->where(
            'name',
            $request->name
        )
        ->exists();

        if ($nameExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Unit already exists.'
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Code
        |--------------------------------------------------------------------------
        */

        if ($request->filled('code')) {

            $codeExists = InventoryUnit::where(
                'school_id',
                $schoolId
            )
            ->where(
                'code',
                $request->code
            )
            ->exists();

            if ($codeExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Unit code already exists.'
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $unit = InventoryUnit::create([

            'school_id' =>
                $schoolId,

            'name' =>
                trim($request->name),

            'code' =>
                $request->code
                    ? trim($request->code)
                    : null,

            'description' =>
                $request->description,

            'status' =>
                $request->has('status')
                    ? $request->boolean('status')
                    : true,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Inventory unit created successfully.',
            'data' => $unit
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $unit = InventoryUnit::where(
            'school_id',
            $this->schoolId()
        )
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $unit
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {
        $schoolId = $this->schoolId();

        $unit = InventoryUnit::where(
            'school_id',
            $schoolId
        )
        ->findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'description' => [
                    'nullable',
                    'string'
                ],

                'status' => [
                    'nullable',
                    'boolean'
                ],
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Name
        |--------------------------------------------------------------------------
        */

        $nameExists = InventoryUnit::where(
            'school_id',
            $schoolId
        )
        ->where(
            'name',
            $request->name
        )
        ->where(
            'id',
            '!=',
            $unit->id
        )
        ->exists();

        if ($nameExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Unit already exists.'
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Code
        |--------------------------------------------------------------------------
        */

        if ($request->filled('code')) {

            $codeExists = InventoryUnit::where(
                'school_id',
                $schoolId
            )
            ->where(
                'code',
                $request->code
            )
            ->where(
                'id',
                '!=',
                $unit->id
            )
            ->exists();

            if ($codeExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Unit code already exists.'
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $unit->update([

            'name' =>
                trim($request->name),

            'code' =>
                $request->code
                    ? trim($request->code)
                    : null,

            'description' =>
                $request->description,

            'status' =>
                $request->has('status')
                    ? $request->boolean('status')
                    : $unit->status,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Inventory unit updated successfully.',
            'data' => $unit
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public function status(
        Request $request,
        $id
    ) {
        $unit = InventoryUnit::where(
            'school_id',
            $this->schoolId()
        )
        ->findOrFail($id);

        $request->validate([
            'status' => [
                'required',
                'boolean'
            ]
        ]);

        $unit->update([
            'status' =>
                $request->boolean('status')
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Inventory unit status updated successfully.',
            'data' => $unit
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $unit = InventoryUnit::where(
            'school_id',
            $this->schoolId()
        )
        ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Don't delete if items exist
        |--------------------------------------------------------------------------
        */

        if ($unit->items()->exists()) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Cannot delete this unit because inventory items are linked to it.'
            ], 422);
        }

        $unit->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Inventory unit deleted successfully.'
        ]);
    }
}