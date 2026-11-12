<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelAttendance extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_attendances';

    protected $fillable = [
        'school_id',
        'student_id',
        'hostel_room_id',
        'attendance_date',
        'status',
        'remarks',
        'marked_by',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'student_id' => 'integer',
        'hostel_room_id' => 'integer',
        'attendance_date' => 'date',
        'marked_by' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'hostel_room_id');
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
