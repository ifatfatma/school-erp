<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'school_id',

        'exam_id',

        'class_id',

        'section_id',

        'subject_id',

        'teacher_id',

        'exam_date',

        'start_time',

        'end_time',

        'room_no',

        'full_marks',

        'pass_marks',

        'instructions',

        'status',

        'created_by',

        'updated_by',

    ];

    protected $casts = [

        'exam_date' => 'date',

        'status' => 'boolean',

        'full_marks' => 'decimal:2',

        'pass_marks' => 'decimal:2',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
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
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
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

    public function getStatusBadgeAttribute()
    {
        return $this->status
            ? 'success'
            : 'danger';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeFilter($query, array $filters)
    {
        return $query

            ->when(
                $filters['exam_id'] ?? null,
                fn($q, $v) => $q->where('exam_id', $v)
            )

            ->when(
                $filters['class_id'] ?? null,
                fn($q, $v) => $q->where('class_id', $v)
            )

            ->when(
                $filters['section_id'] ?? null,
                fn($q, $v) => $q->where('section_id', $v)
            )

            ->when(
                $filters['subject_id'] ?? null,
                fn($q, $v) => $q->where('subject_id', $v)
            )

            ->when(
                $filters['teacher_id'] ?? null,
                fn($q, $v) => $q->where('teacher_id', $v)
            )

            ->when(
                $filters['exam_date'] ?? null,
                fn($q, $v) => $q->whereDate('exam_date', $v)
            )

            ->when(
                isset($filters['status']),
                fn($q, $v) => $q->where('status', $v)
            );
    }

    public function examSubject()
    {
        return $this->hasOne(
            ExamSubject::class,
            'subject_id',
            'subject_id'
        );
    }

    
}