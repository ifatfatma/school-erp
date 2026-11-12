<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'numeric_name',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('numeric_name', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}