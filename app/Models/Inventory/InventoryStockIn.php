<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryStockIn extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_stock_ins';

    protected $fillable = [
        'school_id',
        'inventory_supplier_id',
        'stock_in_no',
        'stock_in_date',
        'invoice_no',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'stock_in_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];


    /*
    |--------------------------------------------------------------------------
    | Supplier
    |--------------------------------------------------------------------------
    */

    public function supplier()
    {
        return $this->belongsTo(
            InventorySupplier::class,
            'inventory_supplier_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Stock In Items
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(
            InventoryStockInItem::class,
            'inventory_stock_in_id'
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