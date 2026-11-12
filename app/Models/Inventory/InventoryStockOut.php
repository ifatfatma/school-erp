<?php

namespace App\Models\Inventory;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryStockOut extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'inventory_stock_outs';

    protected $fillable = [
        'school_id',
        'stock_out_no',
        'stock_out_date',
        'reference_no',
        'reason',
        'total_amount',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'stock_out_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(
            InventoryStockOutItem::class,
            'inventory_stock_out_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'created_by'
        );
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
