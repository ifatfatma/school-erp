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

    public function payment()
    {
        return $this->belongsTo(FeePayment::class);
    }

    public function due()
    {
        return $this->belongsTo(
            StudentFeeDue::class,
            'student_fee_due_id'
        );
    }
}