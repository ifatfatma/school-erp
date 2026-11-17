<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricEnrollment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'person_type',
        'student_id',
        'teacher_id',
        'biometric_pin',
        'rfid_card_no',
        'face_photo_path',
        'face_descriptor',
        'fingerprint_template',
        'is_face_enrolled',
        'is_fingerprint_enrolled',
        'enrolled_at',
        'created_by',
    ];

    protected $casts = [
        'is_face_enrolled' => 'boolean',
        'is_fingerprint_enrolled' => 'boolean',
        'enrolled_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
