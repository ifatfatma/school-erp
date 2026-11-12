<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCopy extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;


    protected $table = 'book_copies';


    protected $fillable = [

        'school_id',

        'book_id',

        'accession_no',

        'barcode',

        'copy_no',

        'shelf_no',

        'rack_no',

        'purchase_date',

        'purchase_price',

        'condition',

        'status',

        'notes',

        'created_by',

        'updated_by',

    ];


    protected $casts = [

        'purchase_date' => 'date',

        'purchase_price' => 'decimal:2',

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
    | Book
    |--------------------------------------------------------------------------
    */

    public function book()
    {
        return $this->belongsTo(
            Book::class
        );
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
    | Available Scope
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable($query)
    {
        return $query->where(
            'status',
            'Available'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Issued Scope
    |--------------------------------------------------------------------------
    */

    public function scopeIssued($query)
    {
        return $query->where(
            'status',
            'Issued'
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
                'accession_no',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'barcode',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'copy_no',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'shelf_no',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'rack_no',
                'like',
                "%{$search}%"
            )

            ->orWhereHas(
                'book',
                function ($bookQuery) use ($search) {

                    $bookQuery->where(
                        'books.title',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'books.book_code',
                        'like',
                        "%{$search}%"
                    );

                }
            );

        });
    }
}