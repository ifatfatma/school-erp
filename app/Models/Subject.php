<?php

namespace App\Models;

class Subject extends BaseModel
{
    protected $fillable = [
        'school_id',
        'name',
        'code',
        'type',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }
}
