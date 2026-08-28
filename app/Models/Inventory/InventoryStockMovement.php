<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryStockMovement extends Model
{
    protected $table = 'inventory_stock_movements';

    protected $fillable = [
        'school_id',
        'inventory_item_id',
        'movement_no',
        'movement_type',
        'quantity',
        'unit_price',
        'total_amount',
        'stock_before',
        'stock_after',
        'movement_date',
        'reference_type',
        'reference_id',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
        'movement_date' => 'date',
    ];


    /*
    |--------------------------------------------------------------------------
    | Inventory Item
    |--------------------------------------------------------------------------
    */

    public function item()
    {
        return $this->belongsTo(
            InventoryItem::class,
            'inventory_item_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Created By
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'created_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | School
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(
            \App\Models\School::class,
            'school_id'
        );
    }
}