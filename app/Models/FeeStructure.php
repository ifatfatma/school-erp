<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStructure extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'class_id',
        'fee_type_id',
        'amount',
        'effective_from',
        'effective_to',
        'due_day',
        'late_fee',
        'status',
        'created_by',
        'updated_by',
    ];

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}