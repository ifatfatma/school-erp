<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventorySupplierController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | School ID
    |--------------------------------------------------------------------------
    */

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

        $query = InventorySupplier::query()
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
                )

                ->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'contact_person',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'phone',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'email',
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

        if ($request->has('status') &&
            $request->status !== '') {

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

        $suppliers = $query
            ->latest()
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );


        return response()->json([
            'success' => true,
            'data' => $suppliers
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

                'contact_person' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:50'
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255'
                ],

                'address' => [
                    'nullable',
                    'string'
                ],

                'city' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'state' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'country' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'tax_number' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'notes' => [
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
                'errors' =>
                    $validator->errors()
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Name
        |--------------------------------------------------------------------------
        */

        $nameExists =
            InventorySupplier::where(
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
                    'Supplier name already exists.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Code
        |--------------------------------------------------------------------------
        */

        if ($request->filled('code')) {

            $codeExists =
                InventorySupplier::where(
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
                        'Supplier code already exists.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Create Supplier
        |--------------------------------------------------------------------------
        */

        $supplier =
            InventorySupplier::create([

                'school_id' =>
                    $schoolId,

                'name' =>
                    trim($request->name),

                'code' =>
                    $request->filled('code')
                        ? trim($request->code)
                        : null,

                'contact_person' =>
                    $request->contact_person,

                'phone' =>
                    $request->phone,

                'email' =>
                    $request->email,

                'address' =>
                    $request->address,

                'city' =>
                    $request->city,

                'state' =>
                    $request->state,

                'country' =>
                    $request->country,

                'tax_number' =>
                    $request->tax_number,

                'notes' =>
                    $request->notes,

                'status' =>
                    $request->has('status')
                        ? $request->boolean('status')
                        : true,

            ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory supplier created successfully.',
            'data' => $supplier
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $supplier =
            InventorySupplier::where(
                'school_id',
                $this->schoolId()
            )
            ->findOrFail($id);


        return response()->json([
            'success' => true,
            'data' => $supplier
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


        $supplier =
            InventorySupplier::where(
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

                'contact_person' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:50'
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255'
                ],

                'address' => [
                    'nullable',
                    'string'
                ],

                'city' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'state' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'country' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'tax_number' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'notes' => [
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
                'errors' =>
                    $validator->errors()
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Name
        |--------------------------------------------------------------------------
        */

        $nameExists =
            InventorySupplier::where(
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
                $supplier->id
            )
            ->exists();


        if ($nameExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Supplier name already exists.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Code
        |--------------------------------------------------------------------------
        */

        if ($request->filled('code')) {

            $codeExists =
                InventorySupplier::where(
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
                    $supplier->id
                )
                ->exists();


            if ($codeExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Supplier code already exists.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $supplier->update([

            'name' =>
                trim($request->name),

            'code' =>
                $request->filled('code')
                    ? trim($request->code)
                    : null,

            'contact_person' =>
                $request->contact_person,

            'phone' =>
                $request->phone,

            'email' =>
                $request->email,

            'address' =>
                $request->address,

            'city' =>
                $request->city,

            'state' =>
                $request->state,

            'country' =>
                $request->country,

            'tax_number' =>
                $request->tax_number,

            'notes' =>
                $request->notes,

            'status' =>
                $request->has('status')
                    ? $request->boolean('status')
                    : $supplier->status,

        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory supplier updated successfully.',
            'data' => $supplier
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
        $supplier =
            InventorySupplier::where(
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


        $supplier->update([
            'status' =>
                $request->boolean('status')
        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory supplier status updated successfully.',
            'data' => $supplier
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $supplier =
            InventorySupplier::where(
                'school_id',
                $this->schoolId()
            )
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Prevent deletion when items are linked
        |--------------------------------------------------------------------------
        */

        if (
            method_exists(
                $supplier,
                'items'
            ) &&
            $supplier->items()->exists()
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Cannot delete this supplier because inventory items are linked to it.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent deletion when purchases are linked
        |--------------------------------------------------------------------------
        */

        if (
            method_exists(
                $supplier,
                'purchases'
            ) &&
            $supplier->purchases()->exists()
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Cannot delete this supplier because inventory purchases are linked to it.'
            ], 422);
        }


        $supplier->delete();


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory supplier deleted successfully.'
        ]);
    }
}