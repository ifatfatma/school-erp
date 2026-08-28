<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportStudent extends Model
{
    use SoftDeletes;

    protected $table = 'transport_students';

    protected $fillable = [
        'school_id',
        'student_id',
        'transport_route_id',
        'transport_route_stop_id',
        'transport_vehicle_id',
        'transport_driver_id',
        'academic_session',
        'pickup_type',
        'transport_fee',
        'start_date',
        'end_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'transport_fee' => 'decimal:2',
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
}