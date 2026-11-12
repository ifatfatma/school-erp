<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSubject extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'exam_id',

        'class_id',

        'subject_id',

        'full_marks',

        'pass_marks',

        'theory_marks',

        'practical_marks',

        'viva_marks',

        'project_marks',

        'display_order',

        'status',

        'created_by',

        'updated_by',

    ];

    protected $casts = [

        'status' => 'boolean',

        'full_marks' => 'decimal:2',

        'pass_marks' => 'decimal:2',

        'theory_marks' => 'decimal:2',

        'practical_marks' => 'decimal:2',

        'viva_marks' => 'decimal:2',

        'project_marks' => 'decimal:2',

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

    public function subject()
    {
        return $this->belongsTo(Subject::class);
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
    | Query Scope
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
                $filters['subject_id'] ?? null,
                fn($q, $v) => $q->where('subject_id', $v)
            )

            ->when(
                isset($filters['status']),
                fn($q, $v) => $q->where('status', $v)
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTotalComponentMarksAttribute()
    {
        return
            $this->theory_marks +
            $this->practical_marks +
            $this->viva_marks +
            $this->project_marks;
    }

    public function getIsConfigurationValidAttribute()
    {
        return
            $this->total_component_marks == $this->full_marks;
    }
}