<?php

namespace App\Http\Resources\Hostel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HostelRoomResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {

        return [

            'id' => $this->id,

            'school_id' =>
                $this->school_id,

            'hostel_building_id' =>
                $this->hostel_building_id,

            'building' => $this->whenLoaded(
                'building',
                function () {

                    return [
                        'id' =>
                            $this->building->id,

                        'name' =>
                            $this->building->name,

                        'code' =>
                            $this->building->code,
                    ];
                }
            ),

            'room_no' =>
                $this->room_no,

            'floor_no' =>
                (int) $this->floor_no,

            'room_type' =>
                $this->room_type,

            'capacity' =>
                (int) $this->capacity,

            'occupied_beds' =>
                (int) $this->occupied_beds,

            'available_beds' =>
                max(
                    0,
                    (int) $this->capacity -
                    (int) $this->occupied_beds
                ),

            'monthly_fee' =>
                number_format(
                    (float) $this->monthly_fee,
                    2,
                    '.',
                    ''
                ),

            'status' =>
                (bool) $this->status,

            'status_label' =>
                $this->status
                    ? 'Active'
                    : 'Inactive',

            'remarks' =>
                $this->remarks,

            'created_at' =>
                $this->created_at
                    ?->format('d/M/Y H:i'),

            'updated_at' =>
                $this->updated_at
                    ?->format('d/M/Y H:i'),
        ];
    }
}