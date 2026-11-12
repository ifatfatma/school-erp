<?php

namespace App\Models\Transport;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportRoute extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'transport_routes';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'start_location',
        'end_location',
        'distance',
        'travel_time',
        'fare',
        'status',
        'remarks',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'fare' => 'decimal:2',
    ];

    public function stops()
    {
        return $this->hasMany(
            TransportRouteStop::class,
            'transport_route_id'
        )->orderBy('stop_order');
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
