<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookIssue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'book_issues';

    protected $fillable = [
        'school_id',
        'library_member_id',
        'book_id',
        'book_copy_id',
        'issue_no',
        'issue_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'issued_by',
        'returned_by',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'due_date'    => 'date',
        'return_date' => 'date',
    ];


    /*
    |--------------------------------------------------------------------------
    | School
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(
            School::class,
            'school_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Library Member
    |--------------------------------------------------------------------------
    */

    public function libraryMember()
    {
        return $this->belongsTo(
            LibraryMember::class,
            'library_member_id'
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
            Book::class,
            'book_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Book Copy
    |--------------------------------------------------------------------------
    */

    public function bookCopy()
    {
        return $this->belongsTo(
            BookCopy::class,
            'book_copy_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Issued By
    |--------------------------------------------------------------------------
    */

    public function issuedBy()
    {
        return $this->belongsTo(
            User::class,
            'issued_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Returned By
    |--------------------------------------------------------------------------
    */

    public function returnedBy()
    {
        return $this->belongsTo(
            User::class,
            'returned_by'
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
    | Returned Scope
    |--------------------------------------------------------------------------
    */

    public function scopeReturned($query)
    {
        return $query->where(
            'status',
            'Returned'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Overdue Scope
    |--------------------------------------------------------------------------
    */

    public function scopeOverdue($query)
    {
        return $query
            ->where(
                'status',
                'Issued'
            )
            ->whereDate(
                'due_date',
                '<',
                now()->toDateString()
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Active Issue
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->whereIn(
            'status',
            [
                'Issued',
                'Overdue',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Is Overdue
    |--------------------------------------------------------------------------
    */

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        if (
            in_array(
                $this->status,
                [
                    'Returned',
                    'Lost',
                    'Damaged',
                ],
                true
            )
        ) {
            return false;
        }

        return $this->due_date->isPast();
    }


    /*
    |--------------------------------------------------------------------------
    | Days Overdue
    |--------------------------------------------------------------------------
    */

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }

        return now()->startOfDay()
            ->diffInDays(
                $this->due_date
            );
    }
}