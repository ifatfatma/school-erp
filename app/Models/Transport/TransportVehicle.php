<?php
namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportVehicle extends Model
{
    use SoftDeletes;

    protected $table = 'transport_vehicles';

    protected $fillable = [
        'school_id',
        'vehicle_no',
        'registration_no',
        'vehicle_type',
        'model',
        'capacity',
        'purchase_date',
        'insurance_expiry',
        'fitness_expiry',
        'pollution_expiry',
        'status',
        'remarks',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'insurance_expiry' => 'date',
        'fitness_expiry' => 'date',
        'pollution_expiry' => 'date',
        'capacity' => 'integer',
    ];
}
