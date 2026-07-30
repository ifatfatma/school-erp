<?php

namespace App\Models;

class Section extends BaseModel
{
    protected $fillable = [
        'school_id',
        'class_id',
        'name',
        'capacity',
        'status'
    ];

    protected $casts = [
        'status'=>'boolean'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class,'class_id');
    }
}