<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelRoomTransfer extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_room_transfers';

    protected $fillable = [
        'school_id',
        'student_id',
        'from_room_id',
        'from_bed_id',
        'to_room_id',
        'to_bed_id',
        'transfer_date',
        'reason',
        'status',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'student_id' => 'integer',
        'from_room_id' => 'integer',
        'from_bed_id' => 'integer',
        'to_room_id' => 'integer',
        'to_bed_id' => 'integer',
        'approved_by' => 'integer',
        'transfer_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function fromRoom()
    {
        return $this->belongsTo(HostelRoom::class, 'from_room_id');
    }

    public function fromBed()
    {
        return $this->belongsTo(HostelBed::class, 'from_bed_id');
    }

    public function toRoom()
    {
        return $this->belongsTo(HostelRoom::class, 'to_room_id');
    }

    public function toBed()
    {
        return $this->belongsTo(HostelBed::class, 'to_bed_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
