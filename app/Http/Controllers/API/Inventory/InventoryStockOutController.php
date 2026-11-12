<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryStockMovement;
use App\Models\Inventory\InventoryStockOut;
use App\Models\Inventory\InventoryStockOutItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryStockOutController extends Controller
{
    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }

    protected function currentStock($schoolId, $itemId)
    {
        $item = InventoryItem::when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('id', $itemId)
            ->firstOrFail();

        $in = InventoryStockMovement::when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('inventory_item_id', $itemId)
            ->whereIn('movement_type', [
                'IN',
                'ADJUSTMENT_IN',
            ])
            ->sum('quantity');

        $out = InventoryStockMovement::when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('inventory_item_id', $itemId)
            ->whereIn('movement_type', [
                'OUT',
                'ADJUSTMENT_OUT',
            ])
            ->sum('quantity');

        return (float) $item->opening_stock
            + (float) $in
            - (float) $out;
    }

    protected function stockOutNo($schoolId)
    {
        $prefix = 'STO-' . now()->format('Ym') . '-';

        $last = InventoryStockOut::when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('stock_out_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $number = $last
            ? ((int) substr($last->stock_out_no, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad(
            $number,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    protected function movementNo($schoolId)
    {
        $prefix = 'MOV-' . now()->format('Ym') . '-';

        $last = InventoryStockMovement::when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('movement_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $number = $last
            ? ((int) substr($last->movement_no, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad(
            $number,
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = InventoryStockOut::where(
            'school_id',
            $schoolId
        )->with([
            'creator:id,name',
            'items.item:id,name,sku,barcode',
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'stock_out_no',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'reference_no',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'reason',
                    'like',
                    "%{$search}%"
                );
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'stock_out_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'stock_out_date',
                '<=',
                $request->to_date
            );
        }

        $results = $query
            ->latest('stock_out_date')
            ->latest('id')
            ->paginate(
                $request->integer('per_page', 20)
            );

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    public function formData()
    {
        $schoolId = $this->schoolId();

        $items = InventoryItem::where(
            'school_id',
            $schoolId
        )
        ->where('status', true)
        ->orderBy('name')
        ->get([
            'id',
            'name',
            'sku',
            'barcode',
            'cost_price',
            'sale_price',
            'opening_stock',
            'reorder_level',
        ]);

        $items->transform(function ($item) use ($schoolId) {
            $item->current_stock =
                $this->currentStock(
                    $schoolId,
                    $item->id
                );

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
            ],
        ]);
    }

    public function show($id)
    {
        $stockOut = InventoryStockOut::where(
            'school_id',
            $this->schoolId()
        )
        ->with([
            'creator:id,name',
            'items.item:id,name,sku,barcode',
        ])
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $stockOut,
        ]);
    }

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();

        $validator = Validator::make(
            $request->all(),
            [
                'stock_out_date' => [
                    'required',
                    'date',
                ],

                'reference_no' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'reason' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

                'status' => [
                    'nullable',
                    'in:Draft,Issued,Cancelled',
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
                    'nullable',
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

        $status = $request->input(
            'status',
            'Issued'
        );

        $stockOut = DB::transaction(function () use (
            $request,
            $schoolId,
            $status
        ) {
            $totalAmount = 0;

            foreach ($request->items as $row) {
                $quantity = (float) $row['quantity'];

                $item = InventoryItem::where(
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

                $stock = $this->currentStock(
                    $schoolId,
                    $item->id
                );

                if (
                    $status === 'Issued' &&
                    $quantity > $stock
                ) {
                    throw new \Exception(
                        "Insufficient stock for {$item->name}. Available stock: {$stock}"
                    );
                }

                $unitPrice = (float) (
                    $row['unit_price']
                    ?? $item->cost_price
                    ?? 0
                );

                $discount = (float) (
                    $row['discount']
                    ?? 0
                );

                $tax = (float) (
                    $row['tax']
                    ?? 0
                );

                $lineTotal = max(
                    0,
                    ($quantity * $unitPrice)
                    - $discount
                    + $tax
                );

                $totalAmount += $lineTotal;
            }

            $stockOut = InventoryStockOut::create([
                'school_id' => $schoolId,

                'stock_out_no' =>
                    $this->stockOutNo(
                        $schoolId
                    ),

                'stock_out_date' =>
                    $request->stock_out_date,

                'reference_no' =>
                    $request->reference_no,

                'reason' =>
                    $request->reason,

                'total_amount' =>
                    $totalAmount,

                'status' =>
                    $status,

                'remarks' =>
                    $request->remarks,

                'created_by' =>
                    auth()->id(),
            ]);

            foreach ($request->items as $row) {

                $item = InventoryItem::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $row['inventory_item_id']
                )
                ->lockForUpdate()
                ->firstOrFail();

                $quantity = (float) $row['quantity'];

                $unitPrice = (float) (
                    $row['unit_price']
                    ?? $item->cost_price
                    ?? 0
                );

                $discount = (float) (
                    $row['discount']
                    ?? 0
                );

                $tax = (float) (
                    $row['tax']
                    ?? 0
                );

                $lineTotal = max(
                    0,
                    ($quantity * $unitPrice)
                    - $discount
                    + $tax
                );

                InventoryStockOutItem::create([
                    'school_id' =>
                        $schoolId,

                    'inventory_stock_out_id' =>
                        $stockOut->id,

                    'inventory_item_id' =>
                        $item->id,

                    'quantity' =>
                        $quantity,

                    'unit_price' =>
                        $unitPrice,

                    'discount' =>
                        $discount,

                    'tax' =>
                        $tax,

                    'total' =>
                        $lineTotal,
                ]);

                if ($status === 'Issued') {

                    $stockBefore =
                        $this->currentStock(
                            $schoolId,
                            $item->id
                        );

                    $stockAfter =
                        $stockBefore -
                        $quantity;

                    InventoryStockMovement::create([
                        'school_id' =>
                            $schoolId,

                        'inventory_item_id' =>
                            $item->id,

                        'movement_no' =>
                            $this->movementNo(
                                $schoolId
                            ),

                        'movement_type' =>
                            'OUT',

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
                            $request->stock_out_date,

                        'reference_type' =>
                            'StockOut',

                        'reference_id' =>
                            $stockOut->id,

                        'remarks' =>
                            $request->remarks,

                        'created_by' =>
                            auth()->id(),
                    ]);
                }
            }

            return $stockOut;
        });

        $stockOut->load([
            'creator:id,name',
            'items.item:id,name,sku,barcode',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Stock Out created successfully.',
            'data' => $stockOut,
        ], 201);
    }
}