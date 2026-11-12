<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteInquiry extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
