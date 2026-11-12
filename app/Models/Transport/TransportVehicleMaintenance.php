<?php

namespace App\Models\Transport;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportVehicleMaintenance extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'transport_vehicle_maintenances';

    protected $fillable = [
        'school_id',
        'transport_vehicle_id',
        'maintenance_no',
        'maintenance_date',
        'maintenance_type',
        'odometer',
        'service_provider',
        'description',
        'cost',
        'next_service_date',
        'next_service_odometer',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_service_date' => 'date',
        'odometer' => 'decimal:2',
        'cost' => 'decimal:2',
        'next_service_odometer' => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
