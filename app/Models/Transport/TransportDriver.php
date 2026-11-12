<?php

namespace App\Models\Transport;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportDriver extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'transport_drivers';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'phone',
        'email',
        'license_no',
        'license_expiry',
        'joining_date',
        'address',
        'remarks',
        'status',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'joining_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
