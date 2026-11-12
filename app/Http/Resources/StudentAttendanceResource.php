<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => $this->school_id,

            'student_id' => $this->student_id,

            'class_id' => $this->class_id,

            'section_id' => $this->section_id,

            'attendance_date' => optional($this->attendance_date)->format('Y-m-d'),

            'status' => $this->status,

            'status_badge' => $this->status_badge,

            'remarks' => $this->remarks,

            'check_in' => $this->check_in
                ? $this->check_in->format('H:i')
                : null,

            'check_out' => $this->check_out
                ? $this->check_out->format('H:i')
                : null,

            'student' => $this->whenLoaded('student', function () {
                return $this->student ? [
                    'id' => $this->student->id,
                    'admission_no' => $this->student->admission_no,
                    'roll_no' => $this->student->roll_no,
                    'name' => trim(
                        $this->student->first_name . ' ' . $this->student->last_name
                    ),
                    'full_name' => $this->student->full_name ?? trim($this->student->first_name . ' ' . $this->student->last_name),
                    'photo' => $this->student->photo_url ?? null,
                ] : null;
            }),

            'class' => $this->whenLoaded('class', function () {
                return $this->class ? [
                    'id' => $this->class->id,
                    'name' => $this->class->name,
                ] : null;
            }),

            'section' => $this->whenLoaded('section', function () {
                return $this->section ? [
                    'id' => $this->section->id,
                    'name' => $this->section->name,
                ] : null;
            }),

            'created_at' => optional($this->created_at)
                ->format('Y-m-d H:i:s'),

            'updated_at' => optional($this->updated_at)
                ->format('Y-m-d H:i:s'),

        ];
    }
}