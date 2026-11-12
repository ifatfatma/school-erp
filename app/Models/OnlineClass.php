<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineClass extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'online_classes';

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'platform',
        'meeting_link',
        'meeting_id',
        'meeting_password',
        'class_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'timezone',
        'status',
        'join_instructions',
        'recorded_link',
        'created_by',
    ];

    protected $casts = [
        'class_date' => 'date:Y-m-d',
        'duration_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'is_live',
        'platform_label',
        'formatted_date',
        'formatted_time_range',
        'timezone_label',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getIsLiveAttribute(): bool
    {
        if ($this->status === 'live') {
            return true;
        }

        if ($this->status === 'cancelled' || $this->status === 'completed') {
            return false;
        }

        try {
            $classDate = Carbon::parse($this->class_date)->format('Y-m-d');
            $start = Carbon::parse($classDate . ' ' . $this->start_time);
            $end = Carbon::parse($classDate . ' ' . $this->end_time);
            $now = Carbon::now();

            return $now->between($start, $end);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getPlatformLabelAttribute(): string
    {
        return match ($this->platform) {
            'google_meet' => 'Google Meet',
            'zoom' => 'Zoom Meeting',
            'teams' => 'Microsoft Teams',
            'jitsi' => 'Free Browser Room (Jitsi)',
            default => 'Online Classroom'
        };
    }

    public function getFormattedDateAttribute(): string
    {
        if (!$this->class_date) return '';
        try {
            return Carbon::parse($this->class_date)->format('D, d M Y');
        } catch (\Throwable $e) {
            return (string) $this->class_date;
        }
    }

    public function getTimezoneLabelAttribute(): string
    {
        $tz = $this->timezone ?: 'Asia/Kolkata';
        return match ($tz) {
            'Asia/Kolkata' => 'IST (GMT+5:30)',
            'Asia/Dubai' => 'GST (GMT+4)',
            'Asia/Dhaka' => 'BST (GMT+6)',
            'Asia/Singapore' => 'SGT (GMT+8)',
            'Asia/Kathmandu' => 'NPT (GMT+5:45)',
            'Europe/London' => 'GMT/BST',
            'America/New_York' => 'EST/EDT',
            'UTC' => 'UTC',
            default => $tz,
        };
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        try {
            $start = Carbon::parse($this->start_time)->format('h:i A');
            $end = Carbon::parse($this->end_time)->format('h:i A');
            $tzShort = match ($this->timezone ?: 'Asia/Kolkata') {
                'Asia/Kolkata' => 'IST',
                'Asia/Dubai' => 'GST',
                'Asia/Dhaka' => 'BST',
                'Asia/Singapore' => 'SGT',
                'Asia/Kathmandu' => 'NPT',
                'Europe/London' => 'GMT',
                'America/New_York' => 'EST',
                'UTC' => 'UTC',
                default => 'IST',
            };
            return "{$start} - {$end} ({$tzShort})";
        } catch (\Throwable $e) {
            return "{$this->start_time} - {$this->end_time}";
        }
    }
}
