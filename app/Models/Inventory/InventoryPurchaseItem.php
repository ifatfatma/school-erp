<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryPurchaseItem extends Model
{
    protected $table = 'inventory_purchase_items';

    protected $fillable = [
        'school_id',
        'inventory_purchase_id',
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

    public function purchase()
    {
        return $this->belongsTo(
            InventoryPurchase::class,
            'inventory_purchase_id'
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