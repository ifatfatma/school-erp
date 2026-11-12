<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAttendance extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'student_id',

        'class_id',

        'section_id',

        'attendance_date',

        'status',

        'remarks',

        'check_in',

        'check_out',

        'created_by',

        'updated_by',

    ];

    protected $casts = [

        'attendance_date' => 'date',

        'check_in' => 'datetime:H:i',

        'check_out' => 'datetime:H:i',

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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {

            'Present' => 'success',

            'Absent' => 'danger',

            'Late' => 'warning',

            'Half Day' => 'info',

            'Leave' => 'secondary',

            default => 'dark',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeFilter($query, array $filters)
    {
        $query

            ->when(
                $filters['class_id'] ?? null,
                fn($q, $value) => $q->where('class_id', $value)
            )

            ->when(
                $filters['section_id'] ?? null,
                fn($q, $value) => $q->where('section_id', $value)
            )

            ->when(
                $filters['attendance_date'] ?? null,
                fn($q, $value) => $q->whereDate('attendance_date', $value)
            )

            ->when(
                $filters['status'] ?? null,
                fn($q, $value) => $q->where('status', $value)
            )

            ->when(
                $filters['student_id'] ?? null,
                fn($q, $value) => $q->where('student_id', $value)
            );
    }
}