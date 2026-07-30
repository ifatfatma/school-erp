<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [

            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],

            'name' => [
                'required',
                'string',
                'max:50',
            ],

            'capacity' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],

        ];
    }

    /**
     * Custom Messages
     */
    public function messages(): array
    {
        return [

            'class_id.required' => 'Please select a class.',

            'class_id.exists' => 'Selected class does not exist.',

            'name.required' => 'Section name is required.',

            'capacity.integer' => 'Capacity must be a number.',

            'capacity.min' => 'Capacity cannot be negative.',

        ];
    }

    /**
     * Prepare data before validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'name' => trim((string) $this->name),

            'status' => filter_var(
                $this->status,
                FILTER_VALIDATE_BOOLEAN
            ),

            'capacity' => $this->capacity ?? 0,

        ]);
    }
}