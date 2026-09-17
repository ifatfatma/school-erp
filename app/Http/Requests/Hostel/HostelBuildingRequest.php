<?php

namespace App\Http\Requests\Hostel;

use Illuminate\Foundation\Http\FormRequest;

class HostelBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        $buildingId = $this->route('building')?->id
            ?? $this->route('hostel_building')?->id
            ?? $this->route('id');

        $schoolId = auth('sanctum')->user()?->school_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:hostel_buildings,name,' . $buildingId . ',id,school_id,' . $schoolId,
            ],

            'code' => [
                'nullable',
                'string',
                'max:255',
            ],

            'building_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'total_floors' => [
                'required',
                'integer',
                'min:1',
            ],

            'total_rooms' => [
                'required',
                'integer',
                'min:0',
            ],

            'total_beds' => [
                'required',
                'integer',
                'min:0',
            ],

            'warden_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'warden_phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => filter_var(
                    $this->status,
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ),
            ]);
        }
    }
}