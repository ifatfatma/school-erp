<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelComplaint extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_complaints';

    protected $fillable = [
        'school_id',
        'student_id',
        'hostel_room_id',
        'title',
        'category',
        'priority',
        'status',
        'description',
        'resolution_notes',
        'resolved_at',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'student_id' => 'integer',
        'hostel_room_id' => 'integer',
        'resolved_at' => 'datetime',
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
}
