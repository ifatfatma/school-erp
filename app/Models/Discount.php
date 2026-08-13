<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'discount_type',
        'value',
        'max_discount',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount' => 'decimal:2',
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
        if ($this->discount_type === 'Percentage') {

            return $this->value . '%';

        }

        return '₹' . number_format(
            (float) $this->value,
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
                $filters['discount_type'] ?? null,
                fn ($q, $value) =>
                    $q->where(
                        'discount_type',
                        $value
                    )
            )

            ->when(
                isset($filters['status']),
                fn ($q) =>
                    $q->where(
                        'status',
                        $filters['status']
                    )
            );
    }
}