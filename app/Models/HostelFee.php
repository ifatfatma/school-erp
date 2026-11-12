<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelFee extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_fees';

    protected $fillable = [
        'school_id',
        'student_id',
        'hostel_room_id',
        'invoice_no',
        'fee_type',
        'month',
        'amount',
        'discount',
        'fine',
        'paid_amount',
        'due_date',
        'paid_date',
        'payment_method',
        'status',
        'remarks',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'student_id' => 'integer',
        'hostel_room_id' => 'integer',
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'fine' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'hostel_room_id');
    }
}
