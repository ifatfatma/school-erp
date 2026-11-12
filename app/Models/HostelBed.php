<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelBed extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_beds';

    protected $fillable = [
        'school_id',
        'hostel_room_id',
        'bed_no',
        'status',
        'monthly_fee',
        'remarks',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'hostel_room_id' => 'integer',
        'monthly_fee' => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'hostel_room_id');
    }

    public function allocation()
    {
        return $this->hasOne(HostelRoomAllocation::class, 'hostel_bed_id')
            ->where('status', 'active');
    }
}
