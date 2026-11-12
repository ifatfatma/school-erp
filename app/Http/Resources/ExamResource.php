<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => $this->school_id,

            'school' => $this->whenLoaded('school', function () {
                return [
                    'id' => $this->school->id,
                    'name' => $this->school->name,
                    'code' => $this->school->code,
                ];
            }),

            'academic_session_id' => $this->academic_session_id,

            'academic_session' => [
                'id'   => $this->academicSession?->id,
                'name' => $this->academicSession?->name,
            ],

            'exam_type_id' => $this->exam_type_id,

            'exam_type' => [
                'id'   => $this->examType?->id,
                'name' => $this->examType?->name,
            ],

            'name' => $this->name,

            'code' => $this->code,

            'description' => $this->description,

            'start_date' => optional($this->start_date)
                ->format('Y-m-d'),

            'end_date' => optional($this->end_date)
                ->format('Y-m-d'),

            'duration' => optional($this->start_date)
                ? $this->start_date->diffInDays($this->end_date) + 1
                : 0,

            'is_marks_locked' => $this->is_marks_locked,

            'marks_lock_label' => $this->is_marks_locked
                ? 'Locked'
                : 'Open',

            'is_result_published' => $this->is_result_published,

            'result_publish_label' => $this->is_result_published
                ? 'Published'
                : 'Draft',

            'status' => $this->status,

            'status_label' => $this->status
                ? 'Active'
                : 'Inactive',

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