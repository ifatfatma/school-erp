<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostelBuilding extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_buildings';

    protected $fillable = [
        'school_id',
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
        'total_floors' => 'integer',
        'total_rooms'  => 'integer',
        'total_beds'   => 'integer',
        'status'       => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function rooms()
    {
        return $this->hasMany(
            HostelRoom::class,
            'hostel_building_id'
        );
    }
}