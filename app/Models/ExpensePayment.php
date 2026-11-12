<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'expense_id',
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
            School::class,
            'school_id'
        );
    }

    public function expense()
    {
        return $this->belongsTo(
            Expense::class,
            'expense_id'
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

    public function scopeFilter(
        $query,
        array $filters
    ) {
        return $query

            ->when(
                $filters['expense_id'] ?? null,
                function ($q, $expenseId) {

                    $q->where(
                        'expense_id',
                        $expenseId
                    );

                }
            )

            ->when(
                $filters['payment_method'] ?? null,
                function ($q, $method) {

                    $q->where(
                        'payment_method',
                        $method
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
                $filters['date_from'] ?? null,
                function ($q, $date) {

                    $q->whereDate(
                        'payment_date',
                        '>=',
                        $date
                    );

                }
            )

            ->when(
                $filters['date_to'] ?? null,
                function ($q, $date) {

                    $q->whereDate(
                        'payment_date',
                        '<=',
                        $date
                    );

                }
            )

            ->when(
                $filters['search'] ?? null,
                function ($q, $search) {

                    $q->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'transaction_no',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'bank_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'cheque_no',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'remarks',
                                'like',
                                "%{$search}%"
                            );

                    });

                }
            );
    }
}