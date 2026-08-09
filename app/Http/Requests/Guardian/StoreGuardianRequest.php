<?php

namespace App\Http\Requests\Guardian;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuardianRequest extends FormRequest
{
    /**
     * Authorize
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
            
            'guardian_type' => [
                'required',
                'in:Father,Mother,Grandfather,Grandmother,Uncle,Aunt,Brother,Sister,Legal Guardian,Other',
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'gender' => [
                'nullable',
                'in:Male,Female,Other',
            ],

            'dob' => [
                'nullable',
                'date',
            ],

            'mobile' => [
                'required',
                'string',
                'max:20',
            ],

            'alternate_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'occupation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'annual_income' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'education' => [
                'nullable',
                'string',
                'max:255',
            ],

            'aadhaar_no' => [
                'nullable',
                'string',
                'max:20',
            ],

            'pan_no' => [
                'nullable',
                'string',
                'max:20',
            ],

            'relation' => [
                'nullable',
                'string',
                'max:100',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'pincode' => [
                'nullable',
                'string',
                'max:10',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'student_ids' => [
                'nullable',
                'array',
            ],

            'student_ids.*' => [
                'exists:students,id',
            ],

        ];
    }

    /**
     * Custom Messages
     */
    public function messages(): array
    {
        return [

            'guardian_type.required' => 'Guardian type is required.',

            'first_name.required' => 'First name is required.',

            'mobile.required' => 'Mobile number is required.',

            'photo.image' => 'Please upload a valid image.',

            'photo.max' => 'Photo size must not exceed 2MB.',

            'student_ids.*.exists' => 'Selected student is invalid.',

        ];
    }
}