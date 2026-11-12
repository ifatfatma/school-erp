<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LateFee extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'late_fee_type',
        'value',
        'grace_days',
        'max_amount',
        'per_day',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'grace_days' => 'integer',
        'per_day' => 'boolean',
        'status' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(
            School::class,
            'school_id'
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
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeAttribute()
    {
        return $this->status
            ? 'success'
            : 'danger';
    }

    public function getFormattedValueAttribute()
    {
        if ($this->late_fee_type === 'Percentage') {
            return $this->value . '%';
        }

        return '₹' . number_format(
            (float) $this->value,
            2
        );
    }


    public function getFormattedMaxAmountAttribute()
    {
        if ($this->max_amount === null) {
            return null;
        }

        return '₹' . number_format(
            (float) $this->max_amount,
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where(
            'status',
            true
        );
    }


    public function scopeFilter(
        $query,
        array $filters
    ) {
        return $query

            ->when(
                $filters['search'] ?? null,
                function ($q, $value) {

                    $q->where(function ($query) use ($value) {

                        $query
                            ->where(
                                'name',
                                'like',
                                "%{$value}%"
                            )
                            ->orWhere(
                                'code',
                                'like',
                                "%{$value}%"
                            );

                    });

                }
            )

            ->when(
                $filters['late_fee_type'] ?? null,
                fn ($q, $value) =>
                    $q->where(
                        'late_fee_type',
                        $value
                    )
            )

            ->when(
                isset($filters['status']),
                fn ($q) =>
                    $q->where(
                        'status',
                        $q->getQuery()
                    )
            );
    }
}