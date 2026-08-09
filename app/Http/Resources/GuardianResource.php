<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => auth()->user()->school_id??0,

            'guardian_type' => $this->guardian_type,

            'first_name' => $this->first_name,

            'last_name' => $this->last_name,

            'full_name' => $this->full_name,

            'gender' => $this->gender,

            'dob' => optional($this->dob)->format('Y-m-d'),

            'mobile' => $this->mobile,

            'alternate_mobile' => $this->alternate_mobile,

            'email' => $this->email,

            'occupation' => $this->occupation,

            'annual_income' => $this->annual_income,

            'education' => $this->education,

            'aadhaar_no' => $this->aadhaar_no,

            'pan_no' => $this->pan_no,

            'relation' => $this->relation,

            'address' => $this->address,

            'city' => $this->city,

            'state' => $this->state,

            'country' => $this->country,

            'pincode' => $this->pincode,

            'photo' => $this->photo,

            'photo_url' => $this->photo_url,

            'status' => (int) $this->status,

            /*
            |--------------------------------------------------------------------------
            | Students
            |--------------------------------------------------------------------------
            */

            'students' => $this->whenLoaded('students', function () {

                return $this->students->map(function ($student) {

                    return [

                        'id' => $student->id,

                        'admission_no' => $student->admission_no,

                        'roll_no' => $student->roll_no,

                        'name' => trim(
                            $student->first_name . ' ' . $student->last_name
                        ),

                        'class_id' => $student->class_id,

                        'section_id' => $student->section_id,

                        'is_primary' => (bool) $student->pivot->is_primary,

                    ];

                });

            }),

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'created_at' => optional($this->created_at)
                ->format('Y-m-d H:i:s'),

            'updated_at' => optional($this->updated_at)
                ->format('Y-m-d H:i:s'),

        ];
    }
}