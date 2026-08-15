<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'expense_category_id',
        'name',
        'payee_name',
        'amount',
        'frequency',
        'start_date',
        'end_date',
        'next_due_date',
        'payment_method',
        'description',
        'auto_create',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
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
                'next_due_date',
                '<=',
                now()->toDateString()
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
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'payee_name',
                                'like',
                                "%{$search}%"
                            );

                    });
                }
            )

            ->when(
                $filters['expense_category_id'] ?? null,
                function ($q, $categoryId) {

                    $q->where(
                        'expense_category_id',
                        $categoryId
                    );
                }
            )

            ->when(
                $filters['frequency'] ?? null,
                function ($q, $frequency) {

                    $q->where(
                        'frequency',
                        $frequency
                    );
                }
            )

            ->when(
                $filters['status'] ?? null,
                function ($q, $status) {

                    $q->where(
                        'status',
                        $status
                    );
                }
            )

            ->when(
                isset($filters['auto_create']) &&
                $filters['auto_create'] !== '',
                function ($q) use ($filters) {

                    $q->where(
                        'auto_create',
                        $filters['auto_create']
                    );
                }
            );
    }
}