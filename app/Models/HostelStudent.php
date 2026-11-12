<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelStudent extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_students';

    protected $fillable = [
        'school_id',
        'student_id',
        'hostel_id',
        'hostel_room_id',
        'hostel_bed_id',
        'admission_date',
        'status',
        'food_preference',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_conditions',
        'remarks',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'student_id' => 'integer',
        'hostel_id' => 'integer',
        'hostel_room_id' => 'integer',
        'hostel_bed_id' => 'integer',
        'admission_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'hostel_room_id');
    }

    public function bed()
    {
        return $this->belongsTo(HostelBed::class, 'hostel_bed_id');
    }

    public function allocations()
    {
        return $this->hasMany(HostelRoomAllocation::class, 'student_id', 'student_id');
    }

    public function fees()
    {
        return $this->hasMany(HostelFee::class, 'student_id', 'student_id');
    }

    public function attendances()
    {
        return $this->hasMany(HostelAttendance::class, 'student_id', 'student_id');
    }

    public function complaints()
    {
        return $this->hasMany(HostelComplaint::class, 'student_id', 'student_id');
    }

    public function visitors()
    {
        return $this->hasMany(HostelVisitor::class, 'student_id', 'student_id');
    }
}
