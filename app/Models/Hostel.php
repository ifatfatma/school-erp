<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hostel extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostels';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'type',
        'warden_name',
        'warden_phone',
        'warden_email',
        'address',
        'description',
        'status',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function buildings()
    {
        return $this->hasMany(HostelBuilding::class, 'hostel_id');
    }

    public function students()
    {
        return $this->hasMany(HostelStudent::class, 'hostel_id');
    }
}
