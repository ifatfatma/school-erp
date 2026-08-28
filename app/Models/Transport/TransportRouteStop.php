<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Model;

class TransportRouteStop extends Model
{
    protected $table = 'transport_route_stops';

    protected $fillable = [
        'school_id',
        'transport_route_id',
        'stop_name',
        'stop_code',
        'stop_order',
        'pickup_time',
        'drop_time',
        'fare',
        'remarks',
    ];

    protected $casts = [
        'stop_order' => 'integer',
        'fare' => 'decimal:2',
    ];

    public function route()
    {
        return $this->belongsTo(
            TransportRoute::class,
            'transport_route_id'
        );
    }
}