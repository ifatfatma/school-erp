<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    const STATUS_PENDING  = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_CANCELLED = 3;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'leave_type_id',
        'from_date',
        'to_date',
        'total_days',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'from_date'   => 'date',
        'to_date'     => 'date',
        'approved_at' => 'datetime',
        'total_days'  => 'decimal:2',
    ];

    protected $appends = [
        'status_text',
        'attachment_url',
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

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING   => 'Pending',
            self::STATUS_APPROVED  => 'Approved',
            self::STATUS_REJECTED  => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            default                => 'Unknown',
        };
    }

    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment) {
            return null;
        }

        return asset('storage/' . $this->attachment);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
}