<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'title',
        'slug',
        'content',
        'publish_date',
        'expiry_date',
        'target_audience',
        'attachment',
        'is_pinned',
        'status',
        'created_by',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expiry_date' => 'date',
        'is_pinned' => 'boolean',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
