<?php

namespace App\Models\Transport;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportAssignment extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'transport_assignments';

    protected $fillable = [
        'school_id',
        'transport_route_id',
        'transport_route_stop_id',
        'transport_vehicle_id',
        'transport_driver_id',
        'assignment_date',
        'start_date',
        'end_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function route()
    {
        return $this->belongsTo(
            TransportRoute::class,
            'transport_route_id'
        );
    }

    public function stop()
    {
        return $this->belongsTo(
            TransportRouteStop::class,
            'transport_route_stop_id'
        );
    }

    public function vehicle()
    {
        return $this->belongsTo(
            TransportVehicle::class,
            'transport_vehicle_id'
        );
    }

    public function driver()
    {
        return $this->belongsTo(
            TransportDriver::class,
            'transport_driver_id'
        );
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
