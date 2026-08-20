<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'school_id',
        'group',
        'key',
        'value',
        'type',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(
            School::class,
            'school_id'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updatedBy()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}