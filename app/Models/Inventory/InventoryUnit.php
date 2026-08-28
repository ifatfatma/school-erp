<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryUnit extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_units';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(
            \App\Models\School::class,
            'school_id'
        );
    }

    public function items()
    {
        return $this->hasMany(
            InventoryItem::class,
            'inventory_unit_id'
        );
    }
}