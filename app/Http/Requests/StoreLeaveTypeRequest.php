<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:leave_types,name,NULL,id,school_id,' . auth()->user()->school_id,

            'description' => 'nullable|string|max:1000',

            'max_days' => 'required|integer|min:1|max:365',

            'is_paid' => 'required|boolean',

            'status' => 'required|boolean',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Leave type name is required.',
            'name.unique' => 'This leave type already exists.',
            'max_days.required' => 'Maximum days is required.',
            'max_days.integer' => 'Maximum days must be a number.',
            'is_paid.required' => 'Please select paid or unpaid.',
            'status.required' => 'Please select status.',
        ];
    }
}