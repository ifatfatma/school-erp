<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryStockMovement;
use Illuminate\Http\Request;

class InventoryStockMovementController extends Controller
{
    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = InventoryStockMovement::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->with([
                'item.category',
                'item.unit',
                'item.supplier',
                'creator'
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
                    'movement_no',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'reference_type',
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
                    function ($q) use ($search) {

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
                    }
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Item
        |--------------------------------------------------------------------------
        */

        if ($request->filled('inventory_item_id')) {

            $query->where(
                'inventory_item_id',
                $request->inventory_item_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Movement Type
        |--------------------------------------------------------------------------
        */

        if ($request->filled('movement_type')) {

            $query->where(
                'movement_type',
                $request->movement_type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $movements = $query
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
            'data' => $movements
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $movement = InventoryStockMovement::query()
            ->where(
                'school_id',
                $this->schoolId()
            )
            ->with([
                'item.category',
                'item.unit',
                'item.supplier',
                'creator'
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $movement
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Item Ledger
    |--------------------------------------------------------------------------
    */

    public function itemLedger(
        Request $request,
        $itemId
    ) {
        $schoolId = $this->schoolId();

        $query = InventoryStockMovement::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'inventory_item_id',
                $itemId
            )
            ->with([
                'item',
                'creator'
            ]);

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

        $movements = $query
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
            'data' => $movements
        ]);
    }
}