<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'category_id',

        'publisher_id',

        'title',

        'book_code',

        'isbn',

        'edition',

        'language',

        'publication_year',

        'pages',

        'price',

        'description',

        'cover_image',

        'status',

        'created_by',

        'updated_by',

    ];


    protected $casts = [

        'publication_year' => 'integer',

        'pages' => 'integer',

        'price' => 'decimal:2',

        'status' => 'boolean',

    ];


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
    | Category
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(
            BookCategory::class,
            'category_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Publisher
    |--------------------------------------------------------------------------
    */

    public function publisher()
    {
        return $this->belongsTo(
            Publisher::class,
            'publisher_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Authors
    |--------------------------------------------------------------------------
    */

    public function authors()
    {
        return $this->belongsToMany(
            Author::class,
            'book_authors',
            'book_id',
            'author_id'
        )->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Created By
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
    | Updated By
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
    | Search Scope
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(
        $query,
        $search
    ) {

        if (!$search) {
            return $query;
        }


        return $query->where(function ($q) use ($search) {

            $q->where(
                'title',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'book_code',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'isbn',
                'like',
                "%{$search}%"
            );

        });
    }

    public function copies()
{
    return $this->hasMany(
        BookCopy::class,
        'book_id'
    );
}
}