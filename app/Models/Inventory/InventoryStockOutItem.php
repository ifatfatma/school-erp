<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryStockOutItem extends Model
{
    protected $table = 'inventory_stock_out_items';

    protected $fillable = [
        'school_id',
        'inventory_stock_out_id',
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

    public function stockOut()
    {
        return $this->belongsTo(
            InventoryStockOut::class,
            'inventory_stock_out_id'
        );
    }

    public function item()
    {
        return $this->belongsTo(
            InventoryItem::class,
            'inventory_item_id'
        );
    }
}