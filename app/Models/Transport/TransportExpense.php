<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportExpense extends Model
{
    use SoftDeletes;

    protected $table = 'transport_expenses';

    protected $fillable = [
        'school_id',
        'transport_vehicle_id',
        'transport_driver_id',
        'expense_no',
        'expense_date',
        'expense_type',
        'quantity',
        'unit',
        'rate',
        'amount',
        'vendor',
        'payment_method',
        'reference_no',
        'description',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
}