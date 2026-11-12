<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomePayment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'income_id',

        'receipt_no',

        'payment_date',

        'amount',

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

        'amount' => 'decimal:2',

    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }


    public function income()
    {
        return $this->belongsTo(
            Income::class
        );
    }


    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSuccess($query)
    {
        return $query->where(
            'status',
            'Success'
        );
    }


    public function scopePending($query)
    {
        return $query->where(
            'status',
            'Pending'
        );
    }


    public function scopeCancelled($query)
    {
        return $query->where(
            'status',
            'Cancelled'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isSuccess(): bool
    {
        return $this->status === 'Success';
    }


    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }


    public function isCancelled(): bool
    {
        return $this->status === 'Cancelled';
    }
}