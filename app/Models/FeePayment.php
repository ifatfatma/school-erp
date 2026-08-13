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

    protected $casts = [
        'payment_date' => 'date',
        'total_amount' => 'decimal:2',
    ];


    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Payment Items
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(
            FeePaymentItem::class,
            'fee_payment_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Creator
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Updater
    |--------------------------------------------------------------------------
    */

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}