<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeCategory extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'name',

        'code',

        'description',

        'status',

        'created_by',

        'updated_by',

    ];


    protected $casts = [

        'status' => 'boolean',

    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(
            School::class
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
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where(
            'status',
            true
        );
    }


    public function scopeSearch(
        $query,
        $search
    ) {

        if (!$search) {
            return $query;
        }


        return $query->where(
            function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );

            }
        );
    }

    public function recurringIncomes()
{
    return $this->hasMany(
        RecurringIncome::class,
        'income_category_id'
    );
}
}