<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringIncome extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'income_category_id',

        'name',

        'reference_no',

        'received_from',

        'amount',

        'payment_method',

        'frequency',

        'start_date',

        'end_date',

        'next_date',

        'auto_create',

        'status',

        'description',

        'created_by',

        'updated_by',

    ];


    protected $casts = [

        'amount' => 'decimal:2',

        'start_date' => 'date',

        'end_date' => 'date',

        'next_date' => 'date',

        'auto_create' => 'boolean',

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

    public function scopeActive($query)
    {
        return $query->where(
            'status',
            'Active'
        );
    }


    public function scopePaused($query)
    {
        return $query->where(
            'status',
            'Paused'
        );
    }


    public function scopeDue($query)
    {
        return $query
            ->where(
                'status',
                'Active'
            )
            ->where(
                'auto_create',
                true
            )
            ->whereDate(
                'next_date',
                '<=',
                now()->toDateString()
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }


    public function isPaused(): bool
    {
        return $this->status === 'Paused';
    }


    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }


    public function isCancelled(): bool
    {
        return $this->status === 'Cancelled';
    }


    public function isDue(): bool
    {
        if (!$this->next_date) {
            return false;
        }

        return $this->status === 'Active'
            && $this->auto_create
            && $this->next_date
                ->lte(now()->startOfDay());
    }


    /*
    |--------------------------------------------------------------------------
    | Display Helpers
    |--------------------------------------------------------------------------
    */

    public function getFormattedAmountAttribute(): string
    {
        return number_format(
            (float) $this->amount,
            2
        );
    }


    public function getFrequencyLabelAttribute(): string
    {
        return $this->frequency;
    }
}