<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePaymentItem extends Model
{
    protected $fillable = [
        'fee_payment_id',
        'student_fee_due_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    public function payment()
    {
        return $this->belongsTo(
            FeePayment::class,
            'fee_payment_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fee Due
    |--------------------------------------------------------------------------
    */

    public function feeDue()
    {
        return $this->belongsTo(
            StudentFeeDue::class,
            'student_fee_due_id'
        );
    }
}