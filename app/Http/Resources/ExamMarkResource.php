<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamMarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'student_id' => $this->student_id,

            'student' => [

                'id' => $this->student?->id,

                'roll_no' => $this->student?->roll_no,

                'admission_no' => $this->student?->admission_no,

                'name' => trim(
                    $this->student?->first_name . ' ' .
                    $this->student?->last_name
                ),

            ],

            'attendance' => $this->attendance,

            'theory_marks' => $this->theory_marks,

            'practical_marks' => $this->practical_marks,

            'viva_marks' => $this->viva_marks,

            'project_marks' => $this->project_marks,

            'total_marks' => $this->total_marks,

            'remarks' => $this->remarks,

            'result' => $this->result_label,

            'is_pass' => $this->is_pass,

        ];
    }
}