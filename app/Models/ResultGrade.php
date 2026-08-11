<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultGrade extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'min_percentage',
        'max_percentage',
        'grade_point',
        'is_pass',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_pass' => 'boolean',
        'status' => 'boolean',
    ];
}