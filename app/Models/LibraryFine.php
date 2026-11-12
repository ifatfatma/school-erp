<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryFine extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'library_fines';

    protected $fillable = [
        'school_id',
        'library_member_id',
        'book_issue_id',
        'book_id',
        'book_copy_id',
        'lost_damaged_book_id',
        'fine_no',
        'fine_date',
        'fine_type',
        'description',
        'overdue_days',
        'fine_per_day',
        'fine_amount',
        'waiver_amount',
        'payable_amount',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'fine_date'      => 'date',
        'overdue_days'   => 'integer',
        'fine_per_day'   => 'decimal:2',
        'fine_amount'    => 'decimal:2',
        'waiver_amount'  => 'decimal:2',
        'payable_amount' => 'decimal:2',
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
    | Lost / Damaged Book
    |--------------------------------------------------------------------------
    */

    public function lostDamagedBook()
    {
        return $this->belongsTo(
            LostDamagedBook::class,
            'lost_damaged_book_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Created By
    |--------------------------------------------------------------------------
    */

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Fine Collections
    |--------------------------------------------------------------------------
    */

    public function collections()
    {
        return $this->hasMany(
            FineCollection::class,
            'library_fine_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Overdue Scope
    |--------------------------------------------------------------------------
    */

    public function scopeOverdue($query)
    {
        return $query->where(
            'fine_type',
            'Overdue'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Lost Scope
    |--------------------------------------------------------------------------
    */

    public function scopeLost($query)
    {
        return $query->where(
            'fine_type',
            'Lost'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Damaged Scope
    |--------------------------------------------------------------------------
    */

    public function scopeDamaged($query)
    {
        return $query->where(
            'fine_type',
            'Damaged'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Pending Scope
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where(
            'status',
            'Pending'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Paid Scope
    |--------------------------------------------------------------------------
    */

    public function scopePaid($query)
    {
        return $query->where(
            'status',
            'Paid'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Partially Paid Scope
    |--------------------------------------------------------------------------
    */

    public function scopePartiallyPaid($query)
    {
        return $query->where(
            'status',
            'Partially Paid'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Waived Scope
    |--------------------------------------------------------------------------
    */

    public function scopeWaived($query)
    {
        return $query->where(
            'status',
            'Waived'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Total Collected
    |--------------------------------------------------------------------------
    */

    public function getCollectedAmountAttribute()
    {
        return (float) $this->collections()
            ->sum('amount');
    }


    /*
    |--------------------------------------------------------------------------
    | Remaining Amount
    |--------------------------------------------------------------------------
    */

    public function getRemainingAmountAttribute()
    {
        return max(
            0,
            (float) $this->payable_amount -
            (float) $this->collected_amount
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Is Fully Paid
    |--------------------------------------------------------------------------
    */

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'Paid';
    }


    /*
    |--------------------------------------------------------------------------
    | Is Pending
    |--------------------------------------------------------------------------
    */

    public function getIsPendingAttribute(): bool
    {
        return in_array(
            $this->status,
            [
                'Pending',
                'Partially Paid',
            ],
            true
        );
    }
}