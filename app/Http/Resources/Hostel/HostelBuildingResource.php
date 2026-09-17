<?php

namespace App\Http\Resources\Hostel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HostelBuildingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_id' => $this->school_id,

            'name' => $this->name,
            'code' => $this->code,
            'building_type' => $this->building_type,

            'total_floors' => $this->total_floors,
            'total_rooms' => $this->total_rooms,
            'total_beds' => $this->total_beds,

            'warden_name' => $this->warden_name,
            'warden_phone' => $this->warden_phone,

            'address' => $this->address,
            'description' => $this->description,

            'status' => (bool) $this->status,
            'status_label' => $this->status ? 'Active' : 'Inactive',

            'created_at' => $this->created_at?->format('d/M/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/M/Y H:i'),
        ];
    }
}