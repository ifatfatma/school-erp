<?php

namespace App\Http\Requests\Hostel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HostelRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        $user = auth('sanctum')->user();

        $schoolId = $user?->school_id;

        $roomId = $this->route('id');

        return [

            'hostel_building_id' => [
                'required',
                'integer',

                Rule::exists(
                    'hostel_buildings',
                    'id'
                )->where(function ($query) use ($schoolId) {

                    $query
                        ->where('school_id', $schoolId)
                        ->whereNull('deleted_at');
                }),
            ],

            'room_no' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'hostel_rooms',
                    'room_no'
                )
                    ->where(function ($query) use ($schoolId) {

                        $query
                            ->where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'hostel_building_id',
                                $this->hostel_building_id
                            )
                            ->whereNull('deleted_at');
                    })
                    ->ignore($roomId),
            ],

            'floor_no' => [
                'required',
                'integer',
                'min:1',
            ],

            'room_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
            ],

            'occupied_beds' => [
                'nullable',
                'integer',
                'min:0',
                'lte:capacity',
            ],

            'monthly_fee' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('occupied_beds')) {
            $this->merge([
                'occupied_beds' => 0,
            ]);
        }

        if (!$this->has('status')) {
            $this->merge([
                'status' => true,
            ]);
        }
    }
}