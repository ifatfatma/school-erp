<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'school_id' => $this->school_id,

            'teacher_id' => $this->teacher_id,

            'teacher' => $this->teacher ? [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
            ] : null,

            'leave_type_id' => $this->leave_type_id,

            'leave_type' => $this->leaveType ? [
                'id' => $this->leaveType->id,
                'name' => $this->leaveType->name,
            ] : null,

            'from_date' => optional($this->from_date)->format('Y-m-d'),

            'to_date' => optional($this->to_date)->format('Y-m-d'),

            'total_days' => $this->total_days,

            'reason' => $this->reason,

            'attachment' => $this->attachment,

            'attachment_url' => $this->attachment_url,

            'status' => $this->status,

            'status_text' => $this->status_text,

            'remarks' => $this->remarks,

            'approved_by' => $this->approved_by,

            'approved_user' => $this->approver ? [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ] : null,

            'approved_at' => optional($this->approved_at)->format('Y-m-d H:i:s'),

            'created_by' => $this->created_by,

            'created_user' => $this->creator ? [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ] : null,

            'updated_by' => $this->updated_by,

            'updated_user' => $this->updater ? [
                'id' => $this->updater->id,
                'name' => $this->updater->name,
            ] : null,

            'created_at' => optional($this->created_at)
                ? $this->created_at->format('Y-m-d H:i:s')
                : null,

            'updated_at' => optional($this->updated_at)
                ? $this->updated_at->format('Y-m-d H:i:s')
                : null,
        ];
    }
}