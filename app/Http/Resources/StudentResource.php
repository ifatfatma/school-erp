<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'academic_session_id' => $this->academic_session_id,
            'school_id' => $this->school_id,

            'admission_no' => $this->admission_no,

            'roll_no' => $this->roll_no,

            'first_name' => $this->first_name,

            'last_name' => $this->last_name,

            'full_name' => $this->full_name,

            'gender' => $this->gender,

            'dob' => optional($this->dob)->format('Y-m-d'),

            'admission_date' => optional($this->admission_date)->format('Y-m-d'),

            'blood_group' => $this->blood_group,

            'religion' => $this->religion,

            'category' => $this->category,

            'aadhaar_no' => $this->aadhaar_no,

            'mobile' => $this->mobile,

            'email' => $this->email,

            'address' => $this->address,

            'father_name' => $this->father_name,

            'father_mobile' => $this->father_mobile,

            'father_occupation' => $this->father_occupation,

            'mother_name' => $this->mother_name,

            'mother_mobile' => $this->mother_mobile,

            'guardian_name' => $this->guardian_name,

            'guardian_mobile' => $this->guardian_mobile,

            'photo' => $this->photo,

            'photo_url' => $this->photo_url,

            'status' => $this->status,

            'status_text' => $this->status ? 'Active' : 'Inactive',

            'school_id' => $this->school_id,
            'school_name' => $this->school?->name ?? 'N/A',
            'school_code' => $this->school?->code ?? '',
            'school' => $this->whenLoaded('school', function () {
                return $this->school ? [
                    'id' => $this->school->id,
                    'name' => $this->school->name,
                    'code' => $this->school->code,
                ] : null;
            }),

            'class_id' => $this->class_id,
            'class_name' => $this->class?->name ?? 'N/A',

            'class' => $this->whenLoaded('class', function () {
                return $this->class ? [
                    'id' => $this->class->id,
                    'name' => $this->class->name,
                ] : null;
            }),

            'section_id' => $this->section_id,
            'section_name' => $this->section?->name ?? 'N/A',

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