<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelRoomAllocation extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_room_allocations';

    protected $fillable = [
        'school_id',
        'student_id',
        'hostel_room_id',
        'hostel_bed_id',
        'allocated_date',
        'vacated_date',
        'monthly_fee',
        'status',
        'remarks',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'student_id' => 'integer',
        'hostel_room_id' => 'integer',
        'hostel_bed_id' => 'integer',
        'allocated_date' => 'date',
        'vacated_date' => 'date',
        'monthly_fee' => 'decimal:2',
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

    public function bed()
    {
        return $this->belongsTo(HostelBed::class, 'hostel_bed_id');
    }
}
