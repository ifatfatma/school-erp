<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\InventoryStockOutItem;
class InventoryItem extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_items';

    protected $fillable = [
        'school_id',
        'inventory_category_id',
        'inventory_unit_id',
        'inventory_supplier_id',
        'name',
        'sku',
        'barcode',
        'opening_stock',
        'reorder_level',
        'cost_price',
        'sale_price',
        'status',
    ];

    protected $casts = [
        'opening_stock' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'status' => 'boolean',
    ];


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


    /*
    |--------------------------------------------------------------------------
    | Category
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(
            InventoryCategory::class,
            'inventory_category_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Unit
    |--------------------------------------------------------------------------
    */

    public function unit()
    {
        return $this->belongsTo(
            InventoryUnit::class,
            'inventory_unit_id'
        );
    }


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

public function stockOutItems()
{
    return $this->hasMany(
        InventoryStockOutItem::class,
        'inventory_item_id'
    );
}

}