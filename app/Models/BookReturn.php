<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'book_returns';

    protected $fillable = [
        'school_id',
        'book_issue_id',
        'library_member_id',
        'book_id',
        'book_copy_id',
        'return_no',
        'return_date',
        'due_date',
        'overdue_days',
        'fine_amount',
        'fine_status',
        'book_condition',
        'remarks',
        'returned_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'due_date' => 'date',
        'overdue_days' => 'integer',
        'fine_amount' => 'decimal:2',
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
    | Book Issue
    |--------------------------------------------------------------------------
    */

    public function bookIssue()
    {
        return $this->belongsTo(
            BookIssue::class,
            'book_issue_id'
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
    | Fine Pending
    |--------------------------------------------------------------------------
    */

    public function scopeFinePending($query)
    {
        return $query->where(
            'fine_status',
            'Pending'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Fine Paid
    |--------------------------------------------------------------------------
    */

    public function scopeFinePaid($query)
    {
        return $query->where(
            'fine_status',
            'Paid'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Overdue
    |--------------------------------------------------------------------------
    */

    public function scopeOverdue($query)
    {
        return $query->where(
            'overdue_days',
            '>',
            0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Damaged
    |--------------------------------------------------------------------------
    */

    public function scopeDamaged($query)
    {
        return $query->where(
            'book_condition',
            'Damaged'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Lost
    |--------------------------------------------------------------------------
    */

    public function scopeLost($query)
    {
        return $query->where(
            'book_condition',
            'Lost'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Has Fine
    |--------------------------------------------------------------------------
    */

    public function getHasFineAttribute(): bool
    {
        return (float) $this->fine_amount > 0;
    }
}