<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelRoom extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_rooms';

    protected $fillable = [
        'school_id',
        'hostel_building_id',
        'room_no',
        'floor_no',
        'room_type',
        'capacity',
        'occupied_beds',
        'monthly_fee',
        'status',
        'remarks',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'hostel_building_id' => 'integer',
        'floor_no' => 'integer',
        'capacity' => 'integer',
        'occupied_beds' => 'integer',
        'monthly_fee' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function building()
    {
        return $this->belongsTo(
            HostelBuilding::class,
            'hostel_building_id'
        );
    }

    public function beds()
    {
        return $this->hasMany(
            HostelBed::class,
            'hostel_room_id'
        );
    }

    public function allocations()
    {
        return $this->hasMany(
            HostelRoomAllocation::class,
            'hostel_room_id'
        );
    }
}