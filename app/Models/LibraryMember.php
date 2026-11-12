<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\User;
class LibraryMember extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'library_members';

    protected $fillable = [
        'school_id',
        'member_no',
        'member_type',
        'student_id',
        'staff_id',
        'phone',
        'email',
        'membership_date',
        'expiry_date',
        'issue_limit',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'membership_date' => 'date',
        'expiry_date' => 'date',
        'status' => 'boolean',
        'issue_limit' => 'integer',
    ];


    public function fineCollections()
    {
        return $this->hasMany(FineCollection::class);
    }

    public function bookIssues()
{
    return $this->hasMany(
        BookIssue::class,
        'library_member_id'
    );
}
    /*
    |--------------------------------------------------------------------------
    | School
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Staff
    |--------------------------------------------------------------------------
    */

    public function staff()
    {
        return $this->belongsTo(
            User::class,
            'staff_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Creator
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Updater
    |--------------------------------------------------------------------------
    */

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Active Scope
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where(
            'status',
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student Scope
    |--------------------------------------------------------------------------
    */

    public function scopeStudents($query)
    {
        return $query->where(
            'member_type',
            'Student'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Staff Scope
    |--------------------------------------------------------------------------
    */

    public function scopeStaff($query)
    {
        return $query->where(
            'member_type',
            'Staff'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(
        $query,
        $search
    ) {
        if (!$search) {
            return $query;
        }

        $search = trim($search);

        return $query->where(function ($q) use ($search) {

            $q->where(
                'library_members.member_no',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'library_members.phone',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'library_members.email',
                'like',
                "%{$search}%"
            )

            ->orWhereHas(
                'student',
                function ($studentQuery) use ($search) {

                    $studentQuery->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                }
            )

            ->orWhereHas(
                'staff',
                function ($staffQuery) use ($search) {

                    $staffQuery->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Is Expired
    |--------------------------------------------------------------------------
    */

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }


    /*
    |--------------------------------------------------------------------------
    | Is Membership Valid
    |--------------------------------------------------------------------------
    */

    public function getIsValidAttribute(): bool
    {
        if (!$this->status) {
            return false;
        }

        if (
            $this->expiry_date &&
            $this->expiry_date->isPast()
        ) {
            return false;
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Display Name
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        if ($this->member_type === 'Student') {

            return $this->student?->name
                ?? $this->member_no;
        }

        return $this->staff?->name
            ?? $this->member_no;
    }
}