<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimetableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,

            'school_id'     => $this->school_id,

            'class_id'      => $this->class_id,
            'class_name'    => $this->class?->name,

            'section_id'    => $this->section_id,
            'section_name'  => $this->section?->name,

            'subject_id'    => $this->subject_id,
            'subject_name'  => $this->subject?->name,

            'teacher_id'    => $this->teacher_id,
            'teacher_name'  => $this->teacher?->first_name,

            'day'           => $this->day,

            'start_time'    => $this->start_time,
            'end_time'      => $this->end_time,

            'room'          => $this->room,

            'status'        => $this->status,

            'created_at'    => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at'    => optional($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}