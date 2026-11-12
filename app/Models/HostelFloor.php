<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelFloor extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_floors';

    protected $fillable = [
        'school_id',
        'hostel_building_id',
        'floor_no',
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'hostel_building_id' => 'integer',
        'floor_no' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function building()
    {
        return $this->belongsTo(HostelBuilding::class, 'hostel_building_id');
    }
}
