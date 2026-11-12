<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class School extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'schools';

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'logo',
        'status',
        'subscription_plan',
        'subscription_status',
        'subscription_expires_at',
        'subscription_features',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'subscription_features' => 'array',
            'subscription_expires_at' => 'datetime',
        ];
    }

    public function canAccessFeature(string $feature): bool
    {
        return \App\Services\SubscriptionPlanService::canAccessFeature($this, $feature);
    }

    public function getActiveFeatures(): array
    {
        return \App\Services\SubscriptionPlanService::getSchoolFeatures($this);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function academicSessions()
    {
        return $this->hasMany(AcademicSession::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassRoom::class, 'school_id');
    }

    public function sections()
    {
        return $this->hasManyThrough(
            Section::class,
            ClassRoom::class,
            'school_id',
            'class_id'
        );
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function parents()
    {
        return $this->hasMany(ParentModel::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function feeCategories()
    {
        return $this->hasMany(FeeCategory::class);
    }

    public function libraryBooks()
    {
        return $this->hasMany(LibraryBook::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function transportRoutes()
    {
        return $this->hasMany(TransportRoute::class);
    }

    public function hostels()
    {
        return $this->hasMany(Hostel::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}