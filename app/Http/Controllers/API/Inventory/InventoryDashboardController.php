<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryCategory;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryStockMovement;
use Illuminate\Http\Request;

class InventoryDashboardController extends Controller
{
    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = InventoryItem::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->with([
                'category:id,name',
                'unit:id,name',
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
        | Category
        |--------------------------------------------------------------------------
        */

        if ($request->filled('inventory_category_id')) {

            $query->where(
                'inventory_category_id',
                $request->inventory_category_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        $items = $query
            ->orderBy('name')
            ->paginate(
                $request->integer('per_page', 20)
            );

        /*
        |--------------------------------------------------------------------------
        | Calculate Stock
        |--------------------------------------------------------------------------
        */

        $items->getCollection()->transform(
            function ($item) use ($schoolId) {

                $in = InventoryStockMovement::where(
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

                $out = InventoryStockMovement::where(
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

                $stockIn = InventoryStockMovement::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'inventory_item_id',
                    $item->id
                )
                ->where(
                    'movement_type',
                    'IN'
                )
                ->sum('quantity');

                $stockOut = InventoryStockMovement::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'inventory_item_id',
                    $item->id
                )
                ->where(
                    'movement_type',
                    'OUT'
                )
                ->sum('quantity');

                $adjustmentIn = InventoryStockMovement::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'inventory_item_id',
                    $item->id
                )
                ->where(
                    'movement_type',
                    'ADJUSTMENT_IN'
                )
                ->sum('quantity');

                $adjustmentOut = InventoryStockMovement::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'inventory_item_id',
                    $item->id
                )
                ->where(
                    'movement_type',
                    'ADJUSTMENT_OUT'
                )
                ->sum('quantity');

                $currentStock =
                    (float) $item->opening_stock
                    + (float) $in
                    - (float) $out;

                $item->stock_in =
                    (float) $stockIn;

                $item->stock_out =
                    (float) $stockOut;

                $item->adjustment_in =
                    (float) $adjustmentIn;

                $item->adjustment_out =
                    (float) $adjustmentOut;

                $item->current_stock =
                    $currentStock;

                $item->stock_value =
                    $currentStock *
                    (float) $item->cost_price;

                $item->is_low_stock =
                    $currentStock <=
                    (float) $item->reorder_level;

                if ($currentStock <= 0) {

                    $item->stock_status =
                        'Out of Stock';

                } elseif (
                    $currentStock <=
                    (float) $item->reorder_level
                ) {

                    $item->stock_status =
                        'Low Stock';

                } else {

                    $item->stock_status =
                        'In Stock';
                }

                return $item;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Low Stock Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->boolean('low_stock')
        ) {

            $filtered =
                $items->getCollection()
                    ->filter(
                        fn ($item) =>
                            $item->is_low_stock
                    )
                    ->values();

            $items->setCollection(
                $filtered
            );
        }

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Summary
    |--------------------------------------------------------------------------
    */

    public function summary()
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
        ->get();

        $totalItems =
            $items->count();

        $totalCategories =
            InventoryCategory::where(
                'school_id',
                $schoolId
            )->count();

        $totalStockIn =
            InventoryStockMovement::where(
                'school_id',
                $schoolId
            )
            ->where(
                'movement_type',
                'IN'
            )
            ->sum('quantity');

        $totalStockOut =
            InventoryStockMovement::where(
                'school_id',
                $schoolId
            )
            ->where(
                'movement_type',
                'OUT'
            )
            ->sum('quantity');

        $adjustmentIn =
            InventoryStockMovement::where(
                'school_id',
                $schoolId
            )
            ->where(
                'movement_type',
                'ADJUSTMENT_IN'
            )
            ->sum('quantity');

        $adjustmentOut =
            InventoryStockMovement::where(
                'school_id',
                $schoolId
            )
            ->where(
                'movement_type',
                'ADJUSTMENT_OUT'
            )
            ->sum('quantity');

        $totalOpeningStock =
            $items->sum(
                fn ($item) =>
                    (float) $item->opening_stock
            );

        $currentStock =
            $totalOpeningStock
            + (float) $totalStockIn
            + (float) $adjustmentIn
            - (float) $totalStockOut
            - (float) $adjustmentOut;

        $lowStockItems = 0;

        $outOfStockItems = 0;

        foreach ($items as $item) {

            $in = InventoryStockMovement::where(
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

            $out = InventoryStockMovement::where(
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

            $stock =
                (float) $item->opening_stock
                + (float) $in
                - (float) $out;

            if ($stock <= 0) {

                $outOfStockItems++;

            } elseif (
                $stock <=
                (float) $item->reorder_level
            ) {

                $lowStockItems++;
            }
        }

        return response()->json([
            'success' => true,

            'data' => [

                'total_items' =>
                    $totalItems,

                'total_categories' =>
                    $totalCategories,

                'opening_stock' =>
                    (float) $totalOpeningStock,

                'stock_in' =>
                    (float) $totalStockIn,

                'stock_out' =>
                    (float) $totalStockOut,

                'adjustment_in' =>
                    (float) $adjustmentIn,

                'adjustment_out' =>
                    (float) $adjustmentOut,

                'current_stock' =>
                    (float) $currentStock,

                'low_stock_items' =>
                    $lowStockItems,

                'out_of_stock_items' =>
                    $outOfStockItems,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    public function categories()
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
                'name',
            ]);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}