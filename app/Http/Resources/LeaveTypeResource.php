<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'name' => $this->name,
            'description' => $this->description,
            'max_days' => $this->max_days,
            'is_paid' => (bool) $this->is_paid,
            'is_paid_text' => $this->is_paid ? 'Paid' : 'Unpaid',
            'status' => (bool) $this->status,
            'status_text' => $this->status ? 'Active' : 'Inactive',
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => optional($this->created_at)->format('d M Y h:i A'),
            'updated_at' => optional($this->updated_at)->format('d M Y h:i A'),
        ];
    }
}