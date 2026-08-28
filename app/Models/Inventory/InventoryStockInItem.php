<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryStockInItem extends Model
{
    protected $table = 'inventory_stock_in_items';

    protected $fillable = [
        'school_id',
        'inventory_stock_in_id',
        'inventory_item_id',
        'quantity',
        'unit_price',
        'discount',
        'tax',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];


    /*
    |--------------------------------------------------------------------------
    | Stock In
    |--------------------------------------------------------------------------
    */

    public function stockIn()
    {
        return $this->belongsTo(
            InventoryStockIn::class,
            'inventory_stock_in_id'
        );
    }


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