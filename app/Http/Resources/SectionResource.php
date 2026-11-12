<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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

            'school' => $this->school ? [
                'id' => $this->school->id,
                'name' => $this->school->name,
                'code' => $this->school->code,
            ] : null,

            'class_id' => $this->class_id,

            'class_name' => $this->class?->name,

            'class' => $this->class ? [
                'id' => $this->class->id,
                'name' => $this->class->name,
            ] : null,

            'name' => $this->name,

            'capacity' => $this->capacity,

            'status' => $this->status,

            'created_at' => optional($this->created_at)
                ->format('Y-m-d H:i:s'),

            'updated_at' => optional($this->updated_at)
                ->format('Y-m-d H:i:s'),

        ];
    }
}