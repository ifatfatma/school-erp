<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'invoice_number',
        'user_id',
        'school_id',
        'current_plan_id',
        'target_plan_id',
        'type',
        'current_plan_price',
        'target_plan_price',
        'credit_amount',
        'net_amount',
        'status',
        'payment_method',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'current_plan_price' => 'decimal:2',
        'target_plan_price' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function currentPlan()
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    public function targetPlan()
    {
        return $this->belongsTo(Plan::class, 'target_plan_id');
    }
}
