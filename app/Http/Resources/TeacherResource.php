<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => $this->school_id,

            'school_name' => $this->school?->name,

            'school_code' => $this->school?->code,

            'school' => $this->whenLoaded('school', function () {
                return [
                    'id' => $this->school->id,
                    'name' => $this->school->name,
                    'code' => $this->school->code,
                ];
            }),

            'employee_id' => $this->employee_id,

            'first_name' => $this->first_name,

            'last_name' => $this->last_name,

            'name' => $this->name,

            'gender' => $this->gender,

            'date_of_birth' => optional($this->date_of_birth)->format('Y-m-d'),

            'email' => $this->email,

            'phone' => $this->phone,

            'qualification' => $this->qualification,

            'experience' => $this->experience,

            'joining_date' => optional($this->joining_date)->format('Y-m-d'),

            'address' => $this->address,

            'photo' => $this->photo
                ? asset('storage/' . $this->photo)
                : null,

            'status' => $this->status,

            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),

            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s'),

        ];
    }
}