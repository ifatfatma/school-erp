<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'school_id',
        'academic_session_id',
        'student_id',
        'receipt_no',
        'payment_date',
        'total_amount',
        'payment_method',
        'transaction_no',
        'bank_name',
        'cheque_no',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function items()
    {
        return $this->hasMany(FeePaymentItem::class);
    }
}