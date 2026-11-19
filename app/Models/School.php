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
        'login_background',
        'status',
        'subscription_plan',
        'subscription_status',
        'subscription_expires_at',
        'subscription_features',
        'custom_domain',
        'domain_status',
        'domain_verified_at',
        'dns_cname_target',
    ];

    protected $appends = [
        'logo_url',
        'login_background_url',
        'website_url',
        'has_custom_domain',
    ];

    public function getHasCustomDomainAttribute(): bool
    {
        return !empty($this->custom_domain);
    }

    public function getWebsiteUrlAttribute(): string
    {
        if (!empty($this->custom_domain) && in_array($this->domain_status, ['active', 'verified'])) {
            $scheme = request()->isSecure() ? 'https://' : 'http://';
            return $scheme . $this->custom_domain;
        }
        return url('/s/' . $this->code);
    }

    public function getLoginBackgroundUrlAttribute(): ?string
    {
        $raw = $this->login_background;
        if (!is_string($raw) || trim($raw) === '' || $raw === 'null' || $raw === '[]' || $raw === '{}') {
            return null;
        }
        $raw = trim($raw);
        if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?/(storage/.*)$#', $raw, $matches)) {
            return '/' . $matches[3];
        }
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }
        return '/storage/' . ltrim($raw, '/');
    }

    public function getLogoUrlAttribute(): ?string
    {
        $raw = $this->logo;
        if (is_array($raw)) {
            $raw = reset($raw);
        }
        if (!is_string($raw)) {
            return null;
        }
        $raw = trim($raw);
        if ($raw === '' || $raw === '[]' || $raw === 'null' || $raw === '{}') {
            return null;
        }
        if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?/(storage/.*)$#', $raw, $matches)) {
            return '/' . $matches[3];
        }
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }
        return '/storage/' . ltrim($raw, '/');
    }

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

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'school_user')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
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