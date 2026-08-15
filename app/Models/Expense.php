<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'expense_category_id',
        'expense_date',
        'expense_no',
        'payee_name',
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
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

   
    protected $appends = [
    'attachment_url',
    'paid_amount',
    'due_amount',
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

    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
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

    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment) {
            return null;
        }

        if (
            filter_var(
                $this->attachment,
                FILTER_VALIDATE_URL
            )
        ) {
            return $this->attachment;
        }

        return Storage::url(
            $this->attachment
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
            'Paid'
        );
    }


    public function scopeFilter(
        $query,
        array $filters
    ) {

        return $query

            ->when(
                $filters['search'] ?? null,
                function ($q, $search) {

                    $q->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'expense_no',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'payee_name',
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
            )

            ->when(
                $filters['expense_category_id'] ?? null,
                fn ($q, $categoryId) =>
                    $q->where(
                        'expense_category_id',
                        $categoryId
                    )
            )

            ->when(
                $filters['payment_method'] ?? null,
                fn ($q, $method) =>
                    $q->where(
                        'payment_method',
                        $method
                    )
            )

            ->when(
                $filters['status'] ?? null,
                fn ($q, $status) =>
                    $q->where(
                        'status',
                        $status
                    )
            )

            ->when(
                $filters['date_from'] ?? null,
                fn ($q, $date) =>
                    $q->whereDate(
                        'expense_date',
                        '>=',
                        $date
                    )
            )

            ->when(
                $filters['date_to'] ?? null,
                fn ($q, $date) =>
                    $q->whereDate(
                        'expense_date',
                        '<=',
                        $date
                    )
            );
    }

    public function payments()
    {
        return $this->hasMany(
            ExpensePayment::class,
            'expense_id'
        );
    }

    public function getPaidAmountAttribute()
{
    return (float) $this->payments()
        ->where('status', 'Success')
        ->sum('amount');
}


public function getDueAmountAttribute()
{
    return max(
        (float) $this->amount -
        (float) $this->paid_amount,
        0
    );
}
}