<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolClassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:classes,name'
            ],

            'numeric_name' => [
                'nullable',
                'integer'
            ],

            'remarks' => [
                'nullable',
                'string'
            ],

            'status' => [
                'required',
                'boolean'
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Class name is required.',
            'name.unique' => 'This class already exists.',
            'status.required' => 'Please select a status.',
        ];
    }
}