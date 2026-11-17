<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricDevice extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'device_type',
        'model',
        'serial_number',
        'ip_address',
        'port',
        'location',
        'status',
        'direction',
        'late_cutoff_time',
        'last_heartbeat_at',
        'api_token',
    ];

    protected $casts = [
        'last_heartbeat_at' => 'datetime',
        'port' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function logs()
    {
        return $this->hasMany(BiometricAttendanceLog::class);
    }
}
