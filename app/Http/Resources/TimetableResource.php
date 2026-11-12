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
        $teacherFullName = $this->teacher ? trim($this->teacher->first_name . ' ' . $this->teacher->last_name) : null;

        return [
            'id'            => $this->id,

            'school_id'     => $this->school_id,
            'school'        => $this->whenLoaded('school', function () {
                return [
                    'id'   => $this->school->id,
                    'name' => $this->school->name,
                    'code' => $this->school->code,
                ];
            }),

            'class_id'      => $this->class_id,
            'class_name'    => $this->class?->name,
            'class'         => $this->class ? [
                'id'   => $this->class->id,
                'name' => $this->class->name,
            ] : null,

            'section_id'    => $this->section_id,
            'section_name'  => $this->section?->name,
            'section'       => $this->section ? [
                'id'   => $this->section->id,
                'name' => $this->section->name,
            ] : null,

            'subject_id'    => $this->subject_id,
            'subject_name'  => $this->subject?->name,
            'subject'       => $this->subject ? [
                'id'   => $this->subject->id,
                'name' => $this->subject->name,
            ] : null,

            'teacher_id'    => $this->teacher_id,
            'teacher_name'  => $teacherFullName,
            'teacher'       => $this->teacher ? [
                'id'         => $this->teacher->id,
                'name'       => $teacherFullName,
                'first_name' => $this->teacher->first_name,
                'last_name'  => $this->teacher->last_name,
            ] : null,

            'day'           => $this->day,

            'start_time'    => $this->start_time,
            'end_time'      => $this->end_time,

            'room'          => $this->room,
            'room_no'       => $this->room,

            'status'        => $this->status,

            'created_at'    => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at'    => optional($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}