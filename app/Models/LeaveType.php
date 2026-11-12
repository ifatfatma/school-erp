<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'max_days',
        'is_paid',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'status' => 'boolean',
        'max_days' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}