<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiBotLog extends Model
{
    use HasFactory;

    protected $table = 'ai_bot_logs';

    protected $fillable = [
        'school_id',
        'user_id',
        'session_id',
        'user_query',
        'bot_response',
        'matched_category',
        'matched_faq_id',
        'confidence_score',
        'was_helpful',
        'escalated_to_whatsapp',
        'user_ip',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'was_helpful' => 'boolean',
        'escalated_to_whatsapp' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function faq()
    {
        return $this->belongsTo(AiBotFaq::class, 'matched_faq_id');
    }
}
