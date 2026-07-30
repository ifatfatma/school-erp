<?php

namespace App\Models;

class School extends BaseModel
{
    protected $table = 'schools';

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
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
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