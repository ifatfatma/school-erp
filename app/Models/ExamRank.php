<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;

class ExamRank extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'exam_id',
        'class_id',
        'section_id',
        'student_id',
        'total_full_marks',
        'total_obtained_marks',
        'percentage',
        'rank',
    ];

    protected $casts = [
        'total_full_marks' => 'float',
        'total_obtained_marks' => 'float',
        'percentage' => 'float',
        'rank' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(
            Student::class
        );
    }

    public function exam()
    {
        return $this->belongsTo(
            Exam::class
        );
    }

    public function class()
    {
        return $this->belongsTo(
            SchoolClass::class,
            'class_id'
        );
    }

    public function section()
    {
        return $this->belongsTo(
            Section::class
        );
    }
}