<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'school_id',

        'income_category_id',

        'income_date',

        'income_no',

        'received_from',

        'payment_method',

        'amount',

        'reference_no',

        'description',

        'attachment',

        'status',

        'created_by',

        'updated_by',

    ];


    protected $casts = [

        'income_date' => 'date',

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


    public function category()
    {
        return $this->belongsTo(
            IncomeCategory::class,
            'income_category_id'
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

    public function scopeReceived($query)
    {
        return $query->where(
            'status',
            'Received'
        );
    }


    public function scopePending($query)
    {
        return $query->where(
            'status',
            'Pending'
        );
    }


    public function scopeActive($query)
    {
        return $query->where(
            'status',
            '!=',
            'Cancelled'
        );
    }


    public function scopeSearch(
        $query,
        $search
    ) {

        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use (
            $search
        ) {

            $q->where(
                'income_no',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'received_from',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'reference_no',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'description',
                'like',
                "%{$search}%"
            );

        });
    }

    public function payments()
    {
        return $this->hasMany(
            IncomePayment::class
        );
    }
}