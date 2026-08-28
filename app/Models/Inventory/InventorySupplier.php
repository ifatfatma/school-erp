<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventorySupplier extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_suppliers';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'tax_number',
        'notes',
        'status',
    ];

    protected $casts = [
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
    | Inventory Items
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(
            InventoryItem::class,
            'inventory_supplier_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */

    public function purchases()
    {
        return $this->hasMany(
            InventoryPurchase::class,
            'inventory_supplier_id'
        );
    }
}