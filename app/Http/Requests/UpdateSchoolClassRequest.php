<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolClassRequest extends FormRequest
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
        $id = $this->route('class');

        return [

            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('classes', 'name')->ignore($id)
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

    public function messages(): array
    {
        return [
            'name.required' => 'Class name is required.',
            'name.unique' => 'This class already exists.',
        ];
    }
}