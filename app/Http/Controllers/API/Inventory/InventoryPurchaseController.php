<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryPurchase;
use App\Models\Inventory\InventoryPurchaseItem;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryPurchaseController extends Controller
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

        $query = InventoryPurchase::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->with([
                'supplier:id,name',
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
                    'purchase_no',
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
                'purchase_date',
                '>=',
                $request->from_date
            );
        }


        if ($request->filled('to_date')) {

            $query->whereDate(
                'purchase_date',
                '<=',
                $request->to_date
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $purchases = $query
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );


        return response()->json([
            'success' => true,
            'data' => $purchases
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


        $suppliers = InventorySupplier::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('status', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code'
            ]);


        $items = InventoryItem::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('status', true)
            ->with([
                'unit:id,name,code',
                'category:id,name'
            ])
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'sku',
                'barcode',
                'cost_price',
                'sale_price',
                'inventory_category_id',
                'inventory_unit_id'
            ]);


        return response()->json([
            'success' => true,
            'data' => [
                'suppliers' => $suppliers,
                'items' => $items
            ]
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE PURCHASE NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generatePurchaseNo(
        $schoolId
    ) {
        $prefix =
            'PUR-' .
            now()->format('Ym') .
            '-';


        $lastPurchase = InventoryPurchase::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where(
                'purchase_no',
                'like',
                $prefix . '%'
            )
            ->orderByDesc('id')
            ->first();


        if (!$lastPurchase) {

            $number = 1;

        } else {

            $lastNo =
                $lastPurchase->purchase_no;

            $lastNumber =
                (int) substr(
                    $lastNo,
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
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();


        $validator = Validator::make(
            $request->all(),
            [

                'inventory_supplier_id' => [
                    'nullable',
                    'integer'
                ],

                'purchase_date' => [
                    'required',
                    'date'
                ],

                'invoice_no' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'discount' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'tax' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'notes' => [
                    'nullable',
                    'string'
                ],

                'status' => [
                    'nullable',
                    'string',
                    'in:Draft,Received,Cancelled'
                ],

                'items' => [
                    'required',
                    'array',
                    'min:1'
                ],

                'items.*.inventory_item_id' => [
                    'required',
                    'integer'
                ],

                'items.*.quantity' => [
                    'required',
                    'numeric',
                    'gt:0'
                ],

                'items.*.unit_price' => [
                    'required',
                    'numeric',
                    'min:0'
                ],

                'items.*.discount' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'items.*.tax' => [
                    'nullable',
                    'numeric',
                    'min:0'
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
        | Supplier Validation
        |--------------------------------------------------------------------------
        */

        if ($request->filled('inventory_supplier_id')) {

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
        | Validate Items
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
            InventoryItem::where(
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
                    'One or more inventory items are invalid.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Totals
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;


        foreach (
            $request->items
            as $row
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
                $lineSubtotal -
                $lineDiscount +
                $lineTax;


            if ($lineTotal < 0) {

                $lineTotal = 0;
            }


            $subtotal += $lineSubtotal;
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
            $subtotal -
            $discount +
            $tax;


        if ($totalAmount < 0) {

            $totalAmount = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        $purchase = DB::transaction(
            function () use (
                $request,
                $schoolId,
                $subtotal,
                $discount,
                $tax,
                $totalAmount
            ) {

                $purchaseNo =
                    $this->generatePurchaseNo(
                        $schoolId
                    );


                $purchase =
                    InventoryPurchase::create([

                        'school_id' =>
                            $schoolId,

                        'inventory_supplier_id' =>
                            $request->inventory_supplier_id,

                        'purchase_no' =>
                            $purchaseNo,

                        'purchase_date' =>
                            $request->purchase_date,

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

                        'notes' =>
                            $request->notes,

                        'status' =>
                            $request->input(
                                'status',
                                'Received'
                            ),

                    ]);


                /*
                |--------------------------------------------------------------------------
                | Purchase Items
                |--------------------------------------------------------------------------
                */

                foreach (
                    $request->items
                    as $row
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
                        $lineSubtotal -
                        $lineDiscount +
                        $lineTax;


                    if ($lineTotal < 0) {

                        $lineTotal = 0;
                    }


                    InventoryPurchaseItem::create([

                        'school_id' =>
                            $schoolId,

                        'inventory_purchase_id' =>
                            $purchase->id,

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


                return $purchase;
            }
        );


        $purchase->load([
            'supplier:id,name',
            'items.item:id,name,sku,barcode'
        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory purchase created successfully.',
            'data' => $purchase
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $purchase =
            InventoryPurchase::query()
            ->where(
                'school_id',
                $this->schoolId()
            )
            ->with([
                'supplier:id,name,code',

                'items.item' => function ($query) {

                    $query->select([
                        'id',
                        'name',
                        'sku',
                        'barcode',
                        'inventory_category_id',
                        'inventory_unit_id'
                    ])
                    ->with([
                        'category:id,name',
                        'unit:id,name,code'
                    ]);

                }
            ])
            ->findOrFail($id);


        return response()->json([
            'success' => true,
            'data' => $purchase
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


        $purchase =
            InventoryPurchase::where(
                'school_id',
                $schoolId
            )
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Prevent editing received purchase
        |--------------------------------------------------------------------------
        |
        | Stock movement integration will be handled by the Stock module.
        | Therefore, received purchases should not be silently changed here.
        |
        */

        if (
            $purchase->status ===
            'Received'
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Received purchases cannot be edited. Cancel this purchase and create a new purchase instead.'
            ], 422);
        }


        $validator = Validator::make(
            $request->all(),
            [

                'inventory_supplier_id' => [
                    'nullable',
                    'integer'
                ],

                'purchase_date' => [
                    'required',
                    'date'
                ],

                'invoice_no' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'discount' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'tax' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'notes' => [
                    'nullable',
                    'string'
                ],

                'status' => [
                    'required',
                    'string',
                    'in:Draft,Received,Cancelled'
                ],

                'items' => [
                    'required',
                    'array',
                    'min:1'
                ],

                'items.*.inventory_item_id' => [
                    'required',
                    'integer'
                ],

                'items.*.quantity' => [
                    'required',
                    'numeric',
                    'gt:0'
                ],

                'items.*.unit_price' => [
                    'required',
                    'numeric',
                    'min:0'
                ],

                'items.*.discount' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'items.*.tax' => [
                    'nullable',
                    'numeric',
                    'min:0'
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
        | Items
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
            InventoryItem::where(
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
                    'One or more inventory items are invalid.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;


        foreach (
            $request->items
            as $row
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


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $request,
                $purchase,
                $schoolId,
                $subtotal,
                $discount,
                $tax,
                $totalAmount
            ) {

                $purchase->update([

                    'inventory_supplier_id' =>
                        $request->inventory_supplier_id,

                    'purchase_date' =>
                        $request->purchase_date,

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

                    'notes' =>
                        $request->notes,

                    'status' =>
                        $request->status,

                ]);


                /*
                |--------------------------------------------------------------------------
                | Replace Purchase Items
                |--------------------------------------------------------------------------
                */

                InventoryPurchaseItem::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'inventory_purchase_id',
                    $purchase->id
                )
                ->delete();


                foreach (
                    $request->items
                    as $row
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


                    InventoryPurchaseItem::create([

                        'school_id' =>
                            $schoolId,

                        'inventory_purchase_id' =>
                            $purchase->id,

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

            }
        );


        $purchase->load([
            'supplier:id,name',
            'items.item:id,name,sku,barcode'
        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory purchase updated successfully.',
            'data' => $purchase
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


        $purchase =
            InventoryPurchase::where(
                'school_id',
                $schoolId
            )
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Received purchases
        |--------------------------------------------------------------------------
        */

        if (
            $purchase->status ===
            'Received'
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Received purchases cannot be deleted.'
            ], 422);
        }


        DB::transaction(
            function () use (
                $purchase,
                $schoolId
            ) {

                InventoryPurchaseItem::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'inventory_purchase_id',
                    $purchase->id
                )
                ->delete();


                $purchase->delete();
            }
        );


        return response()->json([
            'success' => true,
            'message' =>
                'Inventory purchase deleted successfully.'
        ]);
    }
}