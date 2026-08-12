<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'frequency',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function structures()
    {
        return $this->hasMany(FeeStructure::class);
    }
}