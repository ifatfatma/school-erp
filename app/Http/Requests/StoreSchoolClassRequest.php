<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $tenantManager = app(\App\Services\TenantManager::class);
        $schoolId = $this->input('school_id') 
            ?: ($this->user()?->school_id 
            ?: ($tenantManager->getSchoolId() ?: 1));

        return [
            'school_id' => [
                'nullable',
                'integer',
                'exists:schools,id'
            ],

            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('classes', 'name')->where(fn ($q) => $q->where('school_id', $schoolId))
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
            'name.unique' => 'This class already exists for this school.',
            'status.required' => 'Please select a status.',
        ];
    }
}
