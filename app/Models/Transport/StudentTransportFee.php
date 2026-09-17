<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTransportFee extends Model
{
    use SoftDeletes;

    protected $table = 'student_transport_fees';

    protected $fillable = [
        'school_id',
        'student_id',
        'transport_assignment_id',
        'transport_route_id',
        'transport_route_stop_id',
        'amount',
        'discount',
        'start_date',
        'end_date',
        'billing_cycle',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function assignment()
    {
        return $this->belongsTo(
            TransportAssignment::class,
            'transport_assignment_id'
        );
    }

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

    public function student()
{
    return $this->belongsTo(
        \App\Models\Student::class,
        'student_id'
    );
}

}