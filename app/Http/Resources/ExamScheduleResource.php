<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => $this->school_id,

            /*
            |--------------------------------------------------------------------------
            | Exam
            |--------------------------------------------------------------------------
            */

            'exam_id' => $this->exam_id,

            'exam' => [

                'id'   => $this->exam?->id,

                'name' => $this->exam?->name,

            ],

            /*
            |--------------------------------------------------------------------------
            | Class
            |--------------------------------------------------------------------------
            */

            'class_id' => $this->class_id,

            'class' => [

                'id'   => $this->class?->id,

                'name' => $this->class?->name,

            ],

            /*
            |--------------------------------------------------------------------------
            | Section
            |--------------------------------------------------------------------------
            */

            'section_id' => $this->section_id,

            'section' => [

                'id'   => $this->section?->id,

                'name' => $this->section?->name,

            ],

            /*
            |--------------------------------------------------------------------------
            | Subject
            |--------------------------------------------------------------------------
            */

            'subject_id' => $this->subject_id,

            'subject' => [

                'id'   => $this->subject?->id,

                'name' => $this->subject?->name,

                'code' => $this->subject?->code,

            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher
            |--------------------------------------------------------------------------
            */

            'teacher_id' => $this->teacher_id,

            'teacher' => $this->teacher ? [

                'id' => $this->teacher->id,

                'name' => trim(
                    ($this->teacher->first_name ?? '') . ' ' .
                    ($this->teacher->last_name ?? '')
                ),

            ] : null,

            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */

            'exam_date' => optional($this->exam_date)
                ->format('Y-m-d'),

            'start_time' => $this->start_time,

            'end_time' => $this->end_time,

            'room_no' => $this->room_no,

            /*
            |--------------------------------------------------------------------------
            | Marks
            |--------------------------------------------------------------------------
            */

            'full_marks' => (float) $this->full_marks,

            'pass_marks' => (float) $this->pass_marks,

            /*
            |--------------------------------------------------------------------------
            | Other
            |--------------------------------------------------------------------------
            */

            'instructions' => $this->instructions,

            'status' => (bool) $this->status,

            'status_label' => $this->status
                ? 'Active'
                : 'Inactive',

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            'created_by' => [

                'id' => $this->creator?->id,

                'name' => $this->creator?->name,

            ],

            'updated_by' => [

                'id' => $this->updater?->id,

                'name' => $this->updater?->name,

            ],

            'created_at' => optional($this->created_at)
                ->format('d-m-Y H:i'),

            'updated_at' => optional($this->updated_at)
                ->format('d-m-Y H:i'),

        ];
    }
}