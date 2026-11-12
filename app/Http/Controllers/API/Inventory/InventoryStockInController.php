<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryStockIn;
use App\Models\Inventory\InventoryStockInItem;
use App\Models\Inventory\InventoryStockMovement;
use App\Models\Inventory\InventorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryStockInController extends Controller
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

        $query = InventoryStockIn::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->with([
                'supplier:id,name,code',
                'creator:id,name',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'stock_in_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'invoice_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'supplier',
                    function ($supplier) use ($search) {

                        $supplier->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );

                    }
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        if ($request->filled('inventory_supplier_id')) {

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
        | Date Range
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from_date')) {

            $query->whereDate(
                'stock_in_date',
                '>=',
                $request->from_date
            );
        }


        if ($request->filled('to_date')) {

            $query->whereDate(
                'stock_in_date',
                '<=',
                $request->to_date
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $results = $query
            ->latest('stock_in_date')
            ->latest('id')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );


        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FORM DATA
    |--------------------------------------------------------------------------
    */

    public function formData()
    {
        $schoolId = $this->schoolId();


        /*
        |--------------------------------------------------------------------------
        | Suppliers
        |--------------------------------------------------------------------------
        */

        $suppliers = InventorySupplier::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('status', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Items
        |--------------------------------------------------------------------------
        */

        $items = InventoryItem::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('status', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'sku',
                'barcode',
                'inventory_category_id',
                'inventory_unit_id',
                'cost_price',
                'sale_price',
                'opening_stock',
                'reorder_level',
            ]);


        return response()->json([
            'success' => true,
            'data' => [
                'suppliers' => $suppliers,
                'items' => $items,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE STOCK IN NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateStockInNo($schoolId)
    {
        $prefix =
            'STI-' .
            now()->format('Ym') .
            '-';


        $last = InventoryStockIn::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where(
                'stock_in_no',
                'like',
                $prefix . '%'
            )
            ->orderByDesc('id')
            ->first();


        if (!$last) {

            $number = 1;

        } else {

            $lastNumber = (int) substr(
                $last->stock_in_no,
                strlen($prefix)
            );

            $number =
                $lastNumber + 1;
        }


        return $prefix .
            str_pad(
                $number,
                4,
                '0',
                STR_PAD_LEFT
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE MOVEMENT NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateMovementNo($schoolId)
    {
        $prefix =
            'MOV-' .
            now()->format('Ym') .
            '-';


        $last = InventoryStockMovement::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where(
                'movement_no',
                'like',
                $prefix . '%'
            )
            ->orderByDesc('id')
            ->first();


        if (!$last) {

            $number = 1;

        } else {

            $lastNumber = (int) substr(
                $last->movement_no,
                strlen($prefix)
            );

            $number =
                $lastNumber + 1;
        }


        return $prefix .
            str_pad(
                $number,
                6,
                '0',
                STR_PAD_LEFT
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GET CURRENT STOCK
    |--------------------------------------------------------------------------
    */

    protected function currentStock(
        $schoolId,
        $item
    ) {
        $openingStock =
            (float) $item->opening_stock;


        $in = InventoryStockMovement::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'inventory_item_id',
                $item->id
            )
            ->whereIn(
                'movement_type',
                [
                    'IN',
                    'ADJUSTMENT_IN',
                ]
            )
            ->sum('quantity');


        $out = InventoryStockMovement::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'inventory_item_id',
                $item->id
            )
            ->whereIn(
                'movement_type',
                [
                    'OUT',
                    'ADJUSTMENT_OUT',
                ]
            )
            ->sum('quantity');


        return
            $openingStock +
            (float) $in -
            (float) $out;
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validator = Validator::make(
            $request->all(),
            [

                'inventory_supplier_id' => [
                    'nullable',
                    'integer',
                ],

                'stock_in_date' => [
                    'required',
                    'date',
                ],

                'invoice_no' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'discount' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'tax' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

                'status' => [
                    'nullable',
                    'string',
                    'in:Draft,Received,Cancelled',
                ],

                'items' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'items.*.inventory_item_id' => [
                    'required',
                    'integer',
                ],

                'items.*.quantity' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],

                'items.*.unit_price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'items.*.discount' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'items.*.tax' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

            ]
        );


        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Supplier Validation
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'inventory_supplier_id'
            )
        ) {

            $supplierExists =
                InventorySupplier::query()
                ->where(
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
                        'Invalid inventory supplier.',
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Inventory Items
        |--------------------------------------------------------------------------
        */

        $itemIds = collect(
            $request->items
        )
        ->pluck(
            'inventory_item_id'
        )
        ->unique()
        ->values();


        $validItems =
            InventoryItem::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->whereIn(
                'id',
                $itemIds
            )
            ->get([
                'id',
                'name',
                'opening_stock',
            ]);


        if (
            $validItems->count() !==
            $itemIds->count()
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'One or more inventory items are invalid.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Totals
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;


        foreach (
            $request->items as $row
        ) {

            $quantity =
                (float) $row['quantity'];

            $unitPrice =
                (float) $row['unit_price'];


            $subtotal +=
                $quantity *
                $unitPrice;
        }


        $discount =
            (float) (
                $request->discount ??
                0
            );


        $tax =
            (float) (
                $request->tax ??
                0
            );


        $totalAmount =
            max(
                0,
                $subtotal -
                $discount +
                $tax
            );


        $status =
            $request->input(
                'status',
                'Received'
            );


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $stockIn = DB::transaction(
            function () use (
                $request,
                $schoolId,
                $subtotal,
                $discount,
                $tax,
                $totalAmount,
                $status
            ) {

                /*
                |--------------------------------------------------------------------------
                | Create Stock In
                |--------------------------------------------------------------------------
                */

                $stockIn =
                    InventoryStockIn::create([

                        'school_id' =>
                            $schoolId,

                        'inventory_supplier_id' =>
                            $request->inventory_supplier_id,

                        'stock_in_no' =>
                            $this->generateStockInNo(
                                $schoolId
                            ),

                        'stock_in_date' =>
                            $request->stock_in_date,

                        'invoice_no' =>
                            $request->invoice_no,

                        'subtotal' =>
                            $subtotal,

                        'discount' =>
                            $discount,

                        'tax' =>
                            $tax,

                        'total_amount' =>
                            $totalAmount,

                        'status' =>
                            $status,

                        'remarks' =>
                            $request->remarks,

                        'created_by' =>
                            auth()->id(),

                    ]);


                /*
                |--------------------------------------------------------------------------
                | Create Items
                |--------------------------------------------------------------------------
                */

                foreach (
                    $request->items as $row
                ) {

                    $quantity =
                        (float) $row['quantity'];

                    $unitPrice =
                        (float) $row['unit_price'];

                    $lineDiscount =
                        (float) (
                            $row['discount'] ??
                            0
                        );

                    $lineTax =
                        (float) (
                            $row['tax'] ??
                            0
                        );


                    $lineSubtotal =
                        $quantity *
                        $unitPrice;


                    $lineTotal =
                        max(
                            0,
                            $lineSubtotal -
                            $lineDiscount +
                            $lineTax
                        );


                    InventoryStockInItem::create([

                        'school_id' =>
                            $schoolId,

                        'inventory_stock_in_id' =>
                            $stockIn->id,

                        'inventory_item_id' =>
                            $row['inventory_item_id'],

                        'quantity' =>
                            $quantity,

                        'unit_price' =>
                            $unitPrice,

                        'discount' =>
                            $lineDiscount,

                        'tax' =>
                            $lineTax,

                        'total' =>
                            $lineTotal,

                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Create Stock Movements
                |--------------------------------------------------------------------------
                */

                if ($status === 'Received') {

                    foreach (
                        $request->items as $row
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | Lock Item
                        |--------------------------------------------------------------------------
                        */

                        $item =
                            InventoryItem::query()
                            ->where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $row['inventory_item_id']
                            )
                            ->lockForUpdate()
                            ->first();


                        if (!$item) {

                            throw new \Exception(
                                'Inventory item not found.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Current Stock
                        |--------------------------------------------------------------------------
                        */

                        $stockBefore =
                            $this->currentStock(
                                $schoolId,
                                $item
                            );


                        $quantity =
                            (float) $row['quantity'];


                        $unitPrice =
                            (float) $row['unit_price'];


                        $stockAfter =
                            $stockBefore +
                            $quantity;


                        $lineTotal =
                            $quantity *
                            $unitPrice;


                        /*
                        |--------------------------------------------------------------------------
                        | Movement
                        |--------------------------------------------------------------------------
                        */

                        InventoryStockMovement::create([

                            'school_id' =>
                                $schoolId,

                            'inventory_item_id' =>
                                $item->id,

                            'movement_no' =>
                                $this->generateMovementNo(
                                    $schoolId
                                ),

                            'movement_type' =>
                                'IN',

                            'quantity' =>
                                $quantity,

                            'unit_price' =>
                                $unitPrice,

                            'total_amount' =>
                                $lineTotal,

                            'stock_before' =>
                                $stockBefore,

                            'stock_after' =>
                                $stockAfter,

                            'movement_date' =>
                                $request->stock_in_date,

                            'reference_type' =>
                                'StockIn',

                            'reference_id' =>
                                $stockIn->id,

                            'remarks' =>
                                $request->remarks,

                            'created_by' =>
                                auth()->id(),

                        ]);
                    }
                }


                return $stockIn;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Load Response
        |--------------------------------------------------------------------------
        */

        $stockIn->load([
            'supplier:id,name,code',
            'creator:id,name',
            'items.item:id,name,sku,barcode',
        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Stock In created successfully.',
            'data' => $stockIn,
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $schoolId = $this->schoolId();


        $stockIn =
            InventoryStockIn::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->with([
                'supplier:id,name,code',
                'creator:id,name',

                'items.item' => function ($query) {

                    $query
                        ->select([
                            'id',
                            'name',
                            'sku',
                            'barcode',
                            'inventory_category_id',
                            'inventory_unit_id',
                            'cost_price',
                            'sale_price',
                        ])
                        ->with([
                            'category:id,name',
                            'unit:id,name,code',
                        ]);

                },
            ])
            ->findOrFail($id);


        return response()->json([
            'success' => true,
            'data' => $stockIn,
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


        $stockIn =
            InventoryStockIn::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Received Records Cannot Be Edited
        |--------------------------------------------------------------------------
        */

        if (
            $stockIn->status ===
            'Received'
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Received stock entries cannot be edited because stock movements have already been created.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validator = Validator::make(
            $request->all(),
            [

                'inventory_supplier_id' => [
                    'nullable',
                    'integer',
                ],

                'stock_in_date' => [
                    'required',
                    'date',
                ],

                'invoice_no' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'discount' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'tax' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

                'status' => [
                    'required',
                    'string',
                    'in:Draft,Received,Cancelled',
                ],

                'items' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'items.*.inventory_item_id' => [
                    'required',
                    'integer',
                ],

                'items.*.quantity' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],

                'items.*.unit_price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'items.*.discount' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'items.*.tax' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

            ]
        );


        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Validation failed.',
                'errors' =>
                    $validator->errors(),
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
                InventorySupplier::query()
                ->where(
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
                        'Invalid inventory supplier.',
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Item Validation
        |--------------------------------------------------------------------------
        */

        $itemIds = collect(
            $request->items
        )
        ->pluck(
            'inventory_item_id'
        )
        ->unique()
        ->values();


        $validItemCount =
            InventoryItem::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->whereIn(
                'id',
                $itemIds
            )
            ->count();


        if (
            $validItemCount !==
            $itemIds->count()
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'One or more inventory items are invalid.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;


        foreach (
            $request->items as $row
        ) {

            $subtotal +=
                (float) $row['quantity'] *
                (float) $row['unit_price'];
        }


        $discount =
            (float) (
                $request->discount ??
                0
            );


        $tax =
            (float) (
                $request->tax ??
                0
            );


        $totalAmount =
            max(
                0,
                $subtotal -
                $discount +
                $tax
            );


        /*
        |--------------------------------------------------------------------------
        | Update Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $request,
                $stockIn,
                $schoolId,
                $subtotal,
                $discount,
                $tax,
                $totalAmount
            ) {

                $stockIn->update([

                    'inventory_supplier_id' =>
                        $request->inventory_supplier_id,

                    'stock_in_date' =>
                        $request->stock_in_date,

                    'invoice_no' =>
                        $request->invoice_no,

                    'subtotal' =>
                        $subtotal,

                    'discount' =>
                        $discount,

                    'tax' =>
                        $tax,

                    'total_amount' =>
                        $totalAmount,

                    'status' =>
                        $request->status,

                    'remarks' =>
                        $request->remarks,

                ]);


                /*
                |--------------------------------------------------------------------------
                | Delete Existing Items
                |--------------------------------------------------------------------------
                */

                InventoryStockInItem::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'inventory_stock_in_id',
                        $stockIn->id
                    )
                    ->delete();


                /*
                |--------------------------------------------------------------------------
                | Re-create Items
                |--------------------------------------------------------------------------
                */

                foreach (
                    $request->items as $row
                ) {

                    $quantity =
                        (float) $row['quantity'];

                    $unitPrice =
                        (float) $row['unit_price'];

                    $lineDiscount =
                        (float) (
                            $row['discount'] ??
                            0
                        );

                    $lineTax =
                        (float) (
                            $row['tax'] ??
                            0
                        );


                    $lineTotal =
                        max(
                            0,
                            (
                                $quantity *
                                $unitPrice
                            ) -
                            $lineDiscount +
                            $lineTax
                        );


                    InventoryStockInItem::create([

                        'school_id' =>
                            $schoolId,

                        'inventory_stock_in_id' =>
                            $stockIn->id,

                        'inventory_item_id' =>
                            $row['inventory_item_id'],

                        'quantity' =>
                            $quantity,

                        'unit_price' =>
                            $unitPrice,

                        'discount' =>
                            $lineDiscount,

                        'tax' =>
                            $lineTax,

                        'total' =>
                            $lineTotal,

                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | If Draft -> Received
                |--------------------------------------------------------------------------
                */

                if (
                    $request->status ===
                    'Received'
                ) {

                    foreach (
                        $request->items as $row
                    ) {

                        $item =
                            InventoryItem::query()
                            ->where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'id',
                                $row['inventory_item_id']
                            )
                            ->lockForUpdate()
                            ->first();


                        if (!$item) {

                            throw new \Exception(
                                'Inventory item not found.'
                            );
                        }


                        $stockBefore =
                            $this->currentStock(
                                $schoolId,
                                $item
                            );


                        $quantity =
                            (float) $row['quantity'];


                        $unitPrice =
                            (float) $row['unit_price'];


                        $stockAfter =
                            $stockBefore +
                            $quantity;


                        InventoryStockMovement::create([

                            'school_id' =>
                                $schoolId,

                            'inventory_item_id' =>
                                $item->id,

                            'movement_no' =>
                                $this->generateMovementNo(
                                    $schoolId
                                ),

                            'movement_type' =>
                                'IN',

                            'quantity' =>
                                $quantity,

                            'unit_price' =>
                                $unitPrice,

                            'total_amount' =>
                                $quantity *
                                $unitPrice,

                            'stock_before' =>
                                $stockBefore,

                            'stock_after' =>
                                $stockAfter,

                            'movement_date' =>
                                $request->stock_in_date,

                            'reference_type' =>
                                'StockIn',

                            'reference_id' =>
                                $stockIn->id,

                            'remarks' =>
                                $request->remarks,

                            'created_by' =>
                                auth()->id(),

                        ]);
                    }
                }

            }
        );


        $stockIn->load([
            'supplier:id,name,code',
            'creator:id,name',
            'items.item:id,name,sku,barcode',
        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Stock In updated successfully.',
            'data' => $stockIn,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $schoolId = $this->schoolId();


        $stockIn =
            InventoryStockIn::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Received Cannot Be Deleted
        |--------------------------------------------------------------------------
        */

        if (
            $stockIn->status ===
            'Received'
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Received stock entries cannot be deleted because stock movements have already been created.',
            ], 422);
        }


        DB::transaction(
            function () use (
                $stockIn,
                $schoolId
            ) {

                InventoryStockInItem::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'inventory_stock_in_id',
                        $stockIn->id
                    )
                    ->delete();


                $stockIn->delete();
            }
        );


        return response()->json([
            'success' => true,
            'message' =>
                'Stock In deleted successfully.',
        ]);
    }
}