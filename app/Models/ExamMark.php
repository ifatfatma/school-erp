<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamMark extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'exam_schedule_id',

        'student_id',

        'theory_marks',

        'practical_marks',

        'viva_marks',

        'project_marks',

        'total_marks',

        'attendance',

        'remarks',

        'created_by',

        'updated_by',

    ];

    protected $casts = [

        'theory_marks'     => 'decimal:2',

        'practical_marks'  => 'decimal:2',

        'viva_marks'       => 'decimal:2',

        'project_marks'    => 'decimal:2',

        'total_marks'      => 'decimal:2',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function schedule()
    {
        return $this->belongsTo(
            ExamSchedule::class,
            'exam_schedule_id'
        );
    }

    public function examSchedule()
    {
        return $this->schedule();
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
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

    public function getIsPassAttribute()
    {
        $examSubject = optional($this->schedule)
            ->examSubject;

        if (!$examSubject) {
            return null;
        }

        return $this->total_marks >=
            $examSubject->pass_marks;
    }

    public function getResultLabelAttribute()
    {
        if ($this->attendance === 'Absent') {
            return 'Absent';
        }

        return $this->is_pass
            ? 'Pass'
            : 'Fail';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopeFilter($query, array $filters)
    {
        return $query

            ->when(
                $filters['exam_schedule_id'] ?? null,
                fn($q, $v) => $q->where('exam_schedule_id', $v)
            )

            ->when(
                $filters['student_id'] ?? null,
                fn($q, $v) => $q->where('student_id', $v)
            )

            ->when(
                $filters['attendance'] ?? null,
                fn($q, $v) => $q->where('attendance', $v)
            );
    }
}