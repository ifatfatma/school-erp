<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, BelongsToSchool;

    protected $guard_name = 'sanctum';

    protected $fillable = [
        'school_id', 
        'name', 
        'username', 
        'email', 
        'phone', 
        'user_type', 
        'email_verified_at', 
        'password', 
        'status', 
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function guardian()
    {
        return $this->hasOne(Guardian::class);
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        if (in_array(strtolower((string) $this->user_type), ['super_admin', 'super admin'], true)) {
            return true;
        }

        return $this->hasRole('Super Admin', 'sanctum') || $this->hasRole('Super Admin');
    }

    /**
     * Check if user is School Admin.
     */
    public function isSchoolAdmin(): bool
    {
        if (in_array(strtolower((string) $this->user_type), ['school_admin', 'admin'], true)) {
            return true;
        }

        return $this->hasRole('School Admin', 'sanctum') || $this->hasRole('School Admin');
    }
}