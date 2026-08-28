<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryPurchase extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_purchases';

    protected $fillable = [
        'school_id',
        'inventory_supplier_id',
        'purchase_no',
        'purchase_date',
        'invoice_no',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(
            InventorySupplier::class,
            'inventory_supplier_id'
        );
    }

    public function items()
    {
        return $this->hasMany(
            InventoryPurchaseItem::class,
            'inventory_purchase_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'created_by'
        );
    }
}