<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookRenewal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'book_renewals';

    protected $fillable = [
        'school_id',
        'book_issue_id',
        'library_member_id',
        'book_id',
        'book_copy_id',
        'renewal_no',
        'renewal_date',
        'old_due_date',
        'new_due_date',
        'renewal_count',
        'remarks',
        'renewed_by',
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'old_due_date' => 'date',
        'new_due_date' => 'date',
        'renewal_count' => 'integer',
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
    | Renewed By
    |--------------------------------------------------------------------------
    */

    public function renewedBy()
    {
        return $this->belongsTo(
            User::class,
            'renewed_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope: Member
    |--------------------------------------------------------------------------
    */

    public function scopeForMember(
        $query,
        int $memberId
    ) {
        return $query->where(
            'library_member_id',
            $memberId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope: Book
    |--------------------------------------------------------------------------
    */

    public function scopeForBook(
        $query,
        int $bookId
    ) {
        return $query->where(
            'book_id',
            $bookId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope: Issue
    |--------------------------------------------------------------------------
    */

    public function scopeForIssue(
        $query,
        int $issueId
    ) {
        return $query->where(
            'book_issue_id',
            $issueId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Scope: Date Range
    |--------------------------------------------------------------------------
    */

    public function scopeBetweenDates(
        $query,
        $from,
        $to
    ) {
        return $query->whereBetween(
            'renewal_date',
            [
                $from,
                $to,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Accessor: Renewal Days
    |--------------------------------------------------------------------------
    */

    public function getRenewalDaysAttribute()
    {
        if (
            !$this->old_due_date ||
            !$this->new_due_date
        ) {
            return 0;
        }

        return $this->old_due_date
            ->diffInDays(
                $this->new_due_date
            );
    }
}