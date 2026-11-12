<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineAdmission extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'application_no',
        'academic_session_id',
        'class_id',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'blood_group',
        'religion',
        'category',
        'aadhaar_no',
        'mobile',
        'email',
        'address',
        'father_name',
        'father_mobile',
        'father_occupation',
        'mother_name',
        'mother_mobile',
        'guardian_name',
        'guardian_mobile',
        'previous_school',
        'previous_class',
        'previous_marks',
        'status',
        'admin_notes',
        'student_id',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
