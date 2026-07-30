<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => $this->school_id,

            'name' => $this->name,

            'start_date' => $this->start_date?->format('Y-m-d'),

            'end_date' => $this->end_date?->format('Y-m-d'),

            'status' => $this->status,

            'is_current' => $this->is_current,

            'created_at' => $this->created_at?->format('d M Y'),

        ];
    }
}