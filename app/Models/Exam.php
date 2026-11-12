<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'exam_type_id',
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
        'is_marks_locked',
        'is_result_published',
        'status',
        'created_by',
        'updated_by',

    ];
    

    protected $casts = [

        'start_date' => 'date',

        'end_date' => 'date',

        'status' => 'boolean',

        'is_marks_locked' => 'boolean',

        'is_result_published' => 'boolean',

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

    public function academicSession()
    {
        return $this->belongsTo(
            AcademicSession::class,
            'academic_session_id'
        );
    }

    public function examType()
    {
        return $this->belongsTo(
            ExamType::class,
            'exam_type_id'
        );
    }

    public function schedules()
    {
        return $this->hasMany(
            ExamSchedule::class
        );
    }

    public function marks()
    {
        return $this->hasMany(
            ExamMark::class
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
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeAttribute()
    {
        return $this->status
            ? 'success'
            : 'danger';
    }

    public function getMarksLockBadgeAttribute()
    {
        return $this->is_marks_locked
            ? 'danger'
            : 'success';
    }

    public function getResultBadgeAttribute()
    {
        return $this->is_result_published
            ? 'success'
            : 'secondary';
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
                $filters['search'] ?? null,
                function ($q, $value) {

                    $q->where(function ($query) use ($value) {

                        $query

                            ->where('name', 'like', "%{$value}%")

                            ->orWhere('code', 'like', "%{$value}%");

                    });

                }
            )

            ->when(
                $filters['academic_session_id'] ?? null,
                fn($q, $value) =>
                    $q->where('academic_session_id', $value)
            )

            ->when(
                $filters['exam_type_id'] ?? null,
                fn($q, $value) =>
                    $q->where('exam_type_id', $value)
            )

            ->when(
                isset($filters['status']),
                fn($q, $value) =>
                    $q->where('status', $value)
            );
    }
}