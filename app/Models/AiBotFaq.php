<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiBotFaq extends Model
{
    use HasFactory;

    protected $table = 'ai_bot_faqs';

    protected $fillable = [
        'school_id',
        'category',
        'question',
        'answer',
        'keywords',
        'action_label',
        'action_url',
        'is_active',
        'clicks_count',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'clicks_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
