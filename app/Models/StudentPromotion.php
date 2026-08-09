<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPromotion extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'school_id',

        'student_id',

        'from_session_id',
        'to_session_id',

        'from_class_id',
        'to_class_id',

        'from_section_id',
        'to_section_id',

        'promotion_date',

        'promotion_type',

        'remarks',

        'created_by',

        'updated_by',

    ];

    protected $casts = [

        'promotion_date' => 'date',

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

    public function fromSession()
    {
        return $this->belongsTo(
            AcademicSession::class,
            'from_session_id'
        );
    }

    public function toSession()
    {
        return $this->belongsTo(
            AcademicSession::class,
            'to_session_id'
        );
    }

    public function fromClass()
    {
        return $this->belongsTo(
            SchoolClass::class,
            'from_class_id'
        );
    }

    public function toClass()
    {
        return $this->belongsTo(
            SchoolClass::class,
            'to_class_id'
        );
    }

    public function fromSection()
    {
        return $this->belongsTo(
            Section::class,
            'from_section_id'
        );
    }

    public function toSection()
    {
        return $this->belongsTo(
            Section::class,
            'to_section_id'
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

    public function getPromotionBadgeAttribute()
    {
        return match ($this->promotion_type) {

            'Promoted'   => 'success',

            'Repeated'   => 'warning',

            'Transferred'=> 'info',

            'Graduated'  => 'primary',

            'Dropped'    => 'danger',

            default      => 'secondary',

        };
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
                $filters['student_id'] ?? null,
                fn($q, $value) => $q->where('student_id', $value)
            )

            ->when(
                $filters['from_session_id'] ?? null,
                fn($q, $value) => $q->where('from_session_id', $value)
            )

            ->when(
                $filters['to_session_id'] ?? null,
                fn($q, $value) => $q->where('to_session_id', $value)
            )

            ->when(
                $filters['promotion_type'] ?? null,
                fn($q, $value) => $q->where('promotion_type', $value)
            )

            ->when(
                $filters['from_class_id'] ?? null,
                fn($q, $value) => $q->where('from_class_id', $value)
            )

            ->when(
                $filters['to_class_id'] ?? null,
                fn($q, $value) => $q->where('to_class_id', $value)
            )

            ->when(
                $filters['search'] ?? null,
                function ($q, $value) {

                    $q->whereHas('student', function ($student) use ($value) {

                        $student->where('first_name', 'like', "%{$value}%")
                                ->orWhere('last_name', 'like', "%{$value}%")
                                ->orWhere('admission_no', 'like', "%{$value}%")
                                ->orWhere('roll_no', 'like', "%{$value}%");

                    });

                }

            );

    }
}