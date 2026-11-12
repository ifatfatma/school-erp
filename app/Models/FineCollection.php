<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FineCollection extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'fine_collections';

    protected $fillable = [
        'school_id',
        'library_fine_id',
        'library_member_id',
        'collection_no',
        'receipt_no',
        'collection_date',
        'amount',
        'payment_mode',
        'transaction_no',
        'collected_by',
        'remarks',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'library_fine_id' => 'integer',
        'library_member_id' => 'integer',
        'amount' => 'decimal:2',
        'collection_date' => 'date',
        'collected_by' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function fine()
    {
        return $this->belongsTo(LibraryFine::class, 'library_fine_id');
    }

    public function member()
    {
        return $this->belongsTo(LibraryMember::class, 'library_member_id');
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
