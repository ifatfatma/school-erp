<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFeeDue extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'student_id',
        'class_id',
        'section_id',
        'fee_type_id',
        'fee_structure_id',
        'fee_date',
        'due_date',
        'period',
        'amount',
        'discount',
        'late_fee',
        'net_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function payments()
    {
        return $this->hasMany(
            FeePaymentItem::class,
            'student_fee_due_id'
        );
    }
}