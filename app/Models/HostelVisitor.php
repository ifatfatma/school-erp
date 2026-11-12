<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostelVisitor extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'hostel_visitors';

    protected $fillable = [
        'school_id',
        'student_id',
        'visitor_name',
        'relation',
        'visitor_phone',
        'visit_date',
        'in_time',
        'out_time',
        'id_proof_type',
        'id_proof_number',
        'purpose',
        'status',
        'remarks',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'student_id' => 'integer',
        'visit_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
