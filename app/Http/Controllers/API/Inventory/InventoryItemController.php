<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryCategory;
use App\Models\Inventory\InventoryUnit;
use App\Models\Inventory\InventorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryItemController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | School ID
    |--------------------------------------------------------------------------
    */

    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = InventoryItem::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->with([
                'category:id,name',
                'unit:id,name,code',
                'supplier:id,name'
            ]);


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
                    'sku',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'barcode',
                    'like',
                    "%{$search}%"
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'inventory_category_id'
            )
        ) {

            $query->where(
                'inventory_category_id',
                $request->inventory_category_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Unit Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'inventory_unit_id'
            )
        ) {

            $query->where(
                'inventory_unit_id',
                $request->inventory_unit_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Supplier Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'inventory_supplier_id'
            )
        ) {

            $query->where(
                'inventory_supplier_id',
                $request->inventory_supplier_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            $request->has('status') &&
            $request->status !== ''
        ) {

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

        $items = $query
            ->latest()
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );


        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DROPDOWN DATA
    |--------------------------------------------------------------------------
    */

    public function formData()
    {
        $schoolId = $this->schoolId();


        $categories =
            InventoryCategory::where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                true
            )
            ->orderBy('name')
            ->get([
                'id',
                'name'
            ]);


        $units =
            InventoryUnit::where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                true
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code'
            ]);


        $suppliers =
            InventorySupplier::where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                true
            )
            ->orderBy('name')
            ->get([
                'id',
                'name'
            ]);


        return response()->json([
            'success' => true,

            'data' => [
                'categories' =>
                    $categories,

                'units' =>
                    $units,

                'suppliers' =>
                    $suppliers
            ]
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

                'inventory_category_id' => [
                    'required',
                    'integer'
                ],

                'inventory_unit_id' => [
                    'required',
                    'integer'
                ],

                'inventory_supplier_id' => [
                    'nullable',
                    'integer'
                ],

                'name' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'sku' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'barcode' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'opening_stock' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'reorder_level' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'cost_price' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'sale_price' => [
                    'nullable',
                    'numeric',
                    'min:0'
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
                'message' =>
                    'Validation failed.',
                'errors' =>
                    $validator->errors()
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Category
        |--------------------------------------------------------------------------
        */

        $categoryExists =
            InventoryCategory::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->inventory_category_id
            )
            ->exists();


        if (!$categoryExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid inventory category.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Unit
        |--------------------------------------------------------------------------
        */

        $unitExists =
            InventoryUnit::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->inventory_unit_id
            )
            ->exists();


        if (!$unitExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid inventory unit.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Supplier
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'inventory_supplier_id'
            )
        ) {

            $supplierExists =
                InventorySupplier::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $request->inventory_supplier_id
                )
                ->exists();


            if (!$supplierExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Invalid inventory supplier.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate SKU
        |--------------------------------------------------------------------------
        */

        if ($request->filled('sku')) {

            $skuExists =
                InventoryItem::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'sku',
                    $request->sku
                )
                ->exists();


            if ($skuExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'SKU already exists.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Barcode
        |--------------------------------------------------------------------------
        */

        if ($request->filled('barcode')) {

            $barcodeExists =
                InventoryItem::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'barcode',
                    $request->barcode
                )
                ->exists();


            if ($barcodeExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Barcode already exists.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Create Item
        |--------------------------------------------------------------------------
        */

        $item =
            InventoryItem::create([

                'school_id' =>
                    $schoolId,

                'inventory_category_id' =>
                    $request->inventory_category_id,

                'inventory_unit_id' =>
                    $request->inventory_unit_id,

                'inventory_supplier_id' =>
                    $request->inventory_supplier_id,

                'name' =>
                    trim($request->name),

                'sku' =>
                    $request->filled('sku')
                        ? trim($request->sku)
                        : null,

                'barcode' =>
                    $request->filled('barcode')
                        ? trim($request->barcode)
                        : null,

                'opening_stock' =>
                    $request->input(
                        'opening_stock',
                        0
                    ),

                'reorder_level' =>
                    $request->input(
                        'reorder_level',
                        0
                    ),

                'cost_price' =>
                    $request->input(
                        'cost_price',
                        0
                    ),

                'sale_price' =>
                    $request->input(
                        'sale_price',
                        0
                    ),

                'status' =>
                    $request->has('status')
                        ? $request->boolean('status')
                        : true,

            ]);


        $item->load([
            'category:id,name',
            'unit:id,name,code',
            'supplier:id,name'
        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory item created successfully.',
            'data' => $item
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $item =
            InventoryItem::where(
                'school_id',
                $this->schoolId()
            )
            ->with([
                'category:id,name',
                'unit:id,name,code',
                'supplier:id,name'
            ])
            ->findOrFail($id);


        return response()->json([
            'success' => true,
            'data' => $item
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


        $item =
            InventoryItem::where(
                'school_id',
                $schoolId
            )
            ->findOrFail($id);


        $validator = Validator::make(
            $request->all(),
            [

                'inventory_category_id' => [
                    'required',
                    'integer'
                ],

                'inventory_unit_id' => [
                    'required',
                    'integer'
                ],

                'inventory_supplier_id' => [
                    'nullable',
                    'integer'
                ],

                'name' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'sku' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'barcode' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'opening_stock' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'reorder_level' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'cost_price' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'sale_price' => [
                    'nullable',
                    'numeric',
                    'min:0'
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
                'message' =>
                    'Validation failed.',
                'errors' =>
                    $validator->errors()
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        $categoryExists =
            InventoryCategory::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->inventory_category_id
            )
            ->exists();


        if (!$categoryExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid inventory category.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Unit
        |--------------------------------------------------------------------------
        */

        $unitExists =
            InventoryUnit::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->inventory_unit_id
            )
            ->exists();


        if (!$unitExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid inventory unit.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'inventory_supplier_id'
            )
        ) {

            $supplierExists =
                InventorySupplier::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $request->inventory_supplier_id
                )
                ->exists();


            if (!$supplierExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Invalid inventory supplier.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate SKU
        |--------------------------------------------------------------------------
        */

        if ($request->filled('sku')) {

            $skuExists =
                InventoryItem::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'sku',
                    $request->sku
                )
                ->where(
                    'id',
                    '!=',
                    $item->id
                )
                ->exists();


            if ($skuExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'SKU already exists.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Barcode
        |--------------------------------------------------------------------------
        */

        if ($request->filled('barcode')) {

            $barcodeExists =
                InventoryItem::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'barcode',
                    $request->barcode
                )
                ->where(
                    'id',
                    '!=',
                    $item->id
                )
                ->exists();


            if ($barcodeExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Barcode already exists.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $item->update([

            'inventory_category_id' =>
                $request->inventory_category_id,

            'inventory_unit_id' =>
                $request->inventory_unit_id,

            'inventory_supplier_id' =>
                $request->inventory_supplier_id,

            'name' =>
                trim($request->name),

            'sku' =>
                $request->filled('sku')
                    ? trim($request->sku)
                    : null,

            'barcode' =>
                $request->filled('barcode')
                    ? trim($request->barcode)
                    : null,

            'opening_stock' =>
                $request->input(
                    'opening_stock',
                    0
                ),

            'reorder_level' =>
                $request->input(
                    'reorder_level',
                    0
                ),

            'cost_price' =>
                $request->input(
                    'cost_price',
                    0
                ),

            'sale_price' =>
                $request->input(
                    'sale_price',
                    0
                ),

            'status' =>
                $request->has('status')
                    ? $request->boolean('status')
                    : $item->status,

        ]);


        $item->load([
            'category:id,name',
            'unit:id,name,code',
            'supplier:id,name'
        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory item updated successfully.',
            'data' => $item
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
        $item =
            InventoryItem::where(
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


        $item->update([
            'status' =>
                $request->boolean('status')
        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory item status updated successfully.',
            'data' => $item
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $item =
            InventoryItem::where(
                'school_id',
                $this->schoolId()
            )
            ->findOrFail($id);


        $item->delete();


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory item deleted successfully.'
        ]);
    }
}