<?php

namespace App\Models\Inventory;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'inventory_categories';

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
}