<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostelBuilding extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_buildings';

    protected $fillable = [
        'school_id',
        'hostel_id',
        'name',
        'code',
        'building_type',
        'total_floors',
        'total_rooms',
        'total_beds',
        'warden_name',
        'warden_phone',
        'address',
        'description',
        'status',
    ];

    protected $casts = [
        'school_id'    => 'integer',
        'hostel_id'    => 'integer',
        'total_floors' => 'integer',
        'total_rooms'  => 'integer',
        'total_beds'   => 'integer',
        'status'       => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function floors()
    {
        return $this->hasMany(
            HostelFloor::class,
            'hostel_building_id'
        );
    }

    public function rooms()
    {
        return $this->hasMany(
            HostelRoom::class,
            'hostel_building_id'
        );
    }
}