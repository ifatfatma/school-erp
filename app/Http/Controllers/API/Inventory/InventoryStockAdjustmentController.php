<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryStockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryStockAdjustmentController extends Controller
{
    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }

    protected function currentStock($schoolId, $itemId)
    {
        $item = InventoryItem::when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->where('id', $itemId)
            ->first();

        if (!$item) {
            return 0;
        }

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

    protected function movementNo($schoolId)
    {
        $prefix = 'ADJ-' . now()->format('Ym') . '-';

        $last = InventoryStockMovement::where(
            'school_id',
            $schoolId
        )
        ->where(
            'movement_no',
            'like',
            $prefix . '%'
        )
        ->orderByDesc('id')
        ->first();

        $number = $last
            ? ((int) substr(
                $last->movement_no,
                strlen($prefix)
            )) + 1
            : 1;

        return $prefix . str_pad(
            $number,
            5,
            '0',
            STR_PAD_LEFT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Items
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        $schoolId = $this->schoolId();

        $items = InventoryItem::where(
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
            'sku',
            'barcode',
            'cost_price',
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
            'data' => $items,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Adjustment
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();

        $validator = Validator::make(
            $request->all(),
            [
                'inventory_item_id' => [
                    'required',
                    'integer',
                ],

                'adjustment_type' => [
                    'required',
                    'in:ADJUSTMENT_IN,ADJUSTMENT_OUT',
                ],

                'quantity' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],

                'unit_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'movement_date' => [
                    'required',
                    'date',
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
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $movement = DB::transaction(function () use (
                $request,
                $schoolId
            ) {

                $item = InventoryItem::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $request->inventory_item_id
                )
                ->lockForUpdate()
                ->first();

                if (!$item) {
                    throw new \Exception(
                        'Inventory item not found.'
                    );
                }

                $currentStock =
                    $this->currentStock(
                        $schoolId,
                        $item->id
                    );

                $quantity =
                    (float) $request->quantity;

                $type =
                    $request->adjustment_type;

                /*
                |--------------------------------------------------------------------------
                | Prevent negative stock
                |--------------------------------------------------------------------------
                */

                if (
                    $type === 'ADJUSTMENT_OUT' &&
                    $quantity > $currentStock
                ) {

                    throw new \Exception(
                        "Adjustment quantity cannot exceed available stock. Available stock: {$currentStock}"
                    );
                }

                $stockBefore =
                    $currentStock;

                if (
                    $type === 'ADJUSTMENT_IN'
                ) {

                    $stockAfter =
                        $stockBefore +
                        $quantity;

                } else {

                    $stockAfter =
                        $stockBefore -
                        $quantity;

                }

                $unitPrice =
                    (float) (
                        $request->unit_price
                        ?? $item->cost_price
                        ?? 0
                    );

                $totalAmount =
                    $quantity *
                    $unitPrice;

                $remarks =
                    $request->reason
                    ? $request->reason .
                        (
                            $request->remarks
                                ? ' - ' .
                                    $request->remarks
                                : ''
                        )
                    : $request->remarks;

                $movement =
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
                            $type,

                        'quantity' =>
                            $quantity,

                        'unit_price' =>
                            $unitPrice,

                        'total_amount' =>
                            $totalAmount,

                        'stock_before' =>
                            $stockBefore,

                        'stock_after' =>
                            $stockAfter,

                        'movement_date' =>
                            $request->movement_date,

                        'reference_type' =>
                            'StockAdjustment',

                        'reference_id' =>
                            null,

                        'remarks' =>
                            $remarks,

                        'created_by' =>
                            auth()->id(),
                    ]);

                return $movement;
            });

            $movement->load([
                'item:id,name,sku,barcode',
                'creator:id,name',
            ]);

            return response()->json([
                'success' => true,
                'message' =>
                    'Stock adjustment saved successfully.',
                'data' => $movement,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Recent Adjustments
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = InventoryStockMovement::where(
            'school_id',
            $schoolId
        )
        ->whereIn(
            'movement_type',
            [
                'ADJUSTMENT_IN',
                'ADJUSTMENT_OUT',
            ]
        )
        ->with([
            'item:id,name,sku,barcode',
            'creator:id,name',
        ]);

        if ($request->filled('search')) {

            $search =
                trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'movement_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'remarks',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'item',
                    function ($item) use ($search) {

                        $item->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'sku',
                            'like',
                            "%{$search}%"
                        );

                    }
                );

            });
        }

        if ($request->filled('inventory_item_id')) {

            $query->where(
                'inventory_item_id',
                $request->inventory_item_id
            );
        }

        if ($request->filled('movement_type')) {

            $query->where(
                'movement_type',
                $request->movement_type
            );
        }

        if ($request->filled('from_date')) {

            $query->whereDate(
                'movement_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {

            $query->whereDate(
                'movement_date',
                '<=',
                $request->to_date
            );
        }

        $results = $query
            ->latest('movement_date')
            ->latest('id')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}