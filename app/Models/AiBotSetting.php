<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiBotSetting extends Model
{
    use HasFactory;

    protected $table = 'ai_bot_settings';

    protected $fillable = [
        'school_id',
        'bot_name',
        'bot_avatar',
        'welcome_message',
        'whatsapp_number',
        'whatsapp_default_message',
        'support_email',
        'support_phone',
        'office_hours',
        'is_enabled',
        'enable_whatsapp_button',
        'enable_voice_speech',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'enable_whatsapp_button' => 'boolean',
        'enable_voice_speech' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
