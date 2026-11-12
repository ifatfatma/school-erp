<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LostDamagedBook extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'lost_damaged_books';

    protected $fillable = [
        'school_id',
        'book_issue_id',
        'book_copy_id',
        'library_member_id',
        'type',
        'reported_date',
        'book_value',
        'fine_amount',
        'description',
        'status',
        'reported_by',
        'resolved_by',
        'resolved_at',
        'remarks',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'resolved_at' => 'datetime',
        'book_value' => 'decimal:2',
        'fine_amount' => 'decimal:2',
    ];

    public function bookIssue()
    {
        return $this->belongsTo(
            BookIssue::class,
            'book_issue_id'
        );
    }

    public function bookCopy()
    {
        return $this->belongsTo(
            BookCopy::class,
            'book_copy_id'
        );
    }

    public function libraryMember()
    {
        return $this->belongsTo(
            LibraryMember::class,
            'library_member_id'
        );
    }

    public function reportedBy()
    {
        return $this->belongsTo(
            User::class,
            'reported_by'
        );
    }

    public function resolvedBy()
    {
        return $this->belongsTo(
            User::class,
            'resolved_by'
        );
    }
}