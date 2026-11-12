<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => $this->school_id,

            'school' => $this->school ? [
                'id' => $this->school->id,
                'name' => $this->school->name,
                'code' => $this->school->code,
            ] : null,

            'name' => $this->name,

            'code' => $this->code,

            'description' => $this->description,

            'status' => (bool) $this->status,

            'status_label' => $this->status
                ? 'Active'
                : 'Inactive',

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