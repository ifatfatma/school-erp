<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdCardTemplate extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'id_card_templates';

    protected $fillable = [
        'school_id',
        'name',
        'layout',
        'theme',
        'primary_color',
        'accent_color',
        'bg_color',
        'header_title',
        'header_subtitle',
        'contact_info',
        'academic_session',
        'logo_url',
        'signature_url',
        'signatory_title',
        'photo_shape',
        'barcode_type',
        'show_roll_no',
        'show_admission_no',
        'show_class_section',
        'show_blood_group',
        'show_dob',
        'show_father_name',
        'show_emergency_phone',
        'show_address',
        'show_back_side',
        'back_instructions',
        'custom_css',
    ];

    protected $casts = [
        'show_roll_no' => 'boolean',
        'show_admission_no' => 'boolean',
        'show_class_section' => 'boolean',
        'show_blood_group' => 'boolean',
        'show_dob' => 'boolean',
        'show_father_name' => 'boolean',
        'show_emergency_phone' => 'boolean',
        'show_address' => 'boolean',
        'show_back_side' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
