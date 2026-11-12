<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'employee_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'email',
        'phone',
        'qualification',
        'experience',
        'joining_date',
        'address',
        'photo',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'date_of_birth' => 'date',
        'joining_date' => 'date',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}