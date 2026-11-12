<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'section_key',
        'content',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
