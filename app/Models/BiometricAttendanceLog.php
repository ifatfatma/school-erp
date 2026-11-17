<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricAttendanceLog extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'biometric_device_id',
        'person_type',
        'student_id',
        'teacher_id',
        'punch_date',
        'punch_time',
        'punch_type',
        'verification_method',
        'confidence_score',
        'device_serial',
        'snapshot_path',
        'status',
        'attendance_status',
        'sync_status',
        'remarks',
    ];

    protected $casts = [
        'punch_date' => 'date',
        'confidence_score' => 'float',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function device()
    {
        return $this->belongsTo(BiometricDevice::class, 'biometric_device_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
