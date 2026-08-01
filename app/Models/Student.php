<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'school_id',

        'class_id',
        'section_id',

        'admission_no',
        'roll_no',

        'first_name',
        'last_name',

        'gender',
        'dob',
        'admission_date',

        'blood_group',
        'religion',
        'category',

        'aadhaar_no',

        'mobile',
        'email',
        'address',

        'father_name',
        'father_mobile',
        'father_occupation',

        'mother_name',
        'mother_mobile',

        'guardian_name',
        'guardian_mobile',

        'photo',

        'status',

        'created_by',
        'updated_by',
    ];

    protected $casts = [

        'dob' => 'date',

        'admission_date' => 'date',

        'status' => 'boolean',

    ];

    protected $appends = [

        'full_name',

        'photo_url',

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

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' . $this->last_name
        );
    }

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return asset('images/no-image.png');
        }

        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        return Storage::url($this->photo);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {

            $q->where('admission_no', 'like', "%{$search}%")
                ->orWhere('roll_no', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('father_name', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%");
        });
    }

    public function guardians()
    {
        return $this->belongsToMany(
            Guardian::class,
            'student_guardians'
        )->withPivot('is_primary')
            ->withTimestamps();
    }
}
