<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamSubjectResource extends JsonResource
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
            | Marks Configuration
            |--------------------------------------------------------------------------
            */

            'full_marks'       => (float) $this->full_marks,
            'pass_marks'       => (float) $this->pass_marks,
            'theory_marks'     => (float) $this->theory_marks,
            'practical_marks'  => (float) $this->practical_marks,
            'viva_marks'       => (float) $this->viva_marks,
            'project_marks'    => (float) $this->project_marks,

            'total_component_marks' => $this->total_component_marks,

            'configuration_valid' => $this->is_configuration_valid,

            /*
            |--------------------------------------------------------------------------
            | Display
            |--------------------------------------------------------------------------
            */

            'display_order' => $this->display_order,

            'status' => $this->status,

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