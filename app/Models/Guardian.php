<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'guardian_type',

        'first_name',

        'last_name',

        'gender',

        'dob',

        'mobile',

        'alternate_mobile',

        'email',

        'occupation',

        'annual_income',

        'education',

        'aadhaar_no',

        'pan_no',

        'relation',

        'address',

        'city',

        'state',

        'country',

        'pincode',

        'photo',

        'user_id',

        'status',

        'created_by',

        'updated_by',

    ];

    protected $casts = [

        'dob' => 'date',

        'status' => 'boolean',

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'student_guardians'
        )->withPivot('is_primary')
         ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return null;
        }

        return asset('storage/' . $this->photo);
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

                        $query->where('first_name', 'like', "%{$value}%")
                              ->orWhere('last_name', 'like', "%{$value}%")
                              ->orWhere('mobile', 'like', "%{$value}%")
                              ->orWhere('email', 'like', "%{$value}%");

                    });

                }
            )

            ->when(
                $filters['guardian_type'] ?? null,
                fn($q, $value) => $q->where('guardian_type', $value)
            )

            ->when(
                isset($filters['status']) && $filters['status'] !== '',
                fn($q) => $q->where('status', $filters['status'])
            );
    }
}