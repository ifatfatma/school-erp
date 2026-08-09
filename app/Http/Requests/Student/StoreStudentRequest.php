<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_session_id' => [
                'required',
                'exists:academic_sessions,id',
            ],
            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'admission_no' => [
                'nullable',
                'string',
                'max:50',
                'unique:students,admission_no',
            ],

            'roll_no' => [
                'nullable',
                'string',
                'max:50',
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
                'required',
                Rule::in([
                    'Male',
                    'Female',
                    'Other'
                ]),
            ],

            'dob' => [
                'nullable',
                'date',
            ],

            'admission_date' => [
                'required',
                'date',
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:10',
            ],

            'religion' => [
                'nullable',
                'string',
                'max:100',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'aadhaar_no' => [
                'nullable',
                'digits:12',
            ],

            'mobile' => [
                'nullable',
                'digits_between:10,15',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'father_name' => [
                'required',
                'string',
                'max:150',
            ],

            'father_mobile' => [
                'nullable',
                'digits_between:10,15',
            ],

            'father_occupation' => [
                'nullable',
                'string',
                'max:100',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'mother_mobile' => [
                'nullable',
                'digits_between:10,15',
            ],

            'guardian_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'guardian_mobile' => [
                'nullable',
                'digits_between:10,15',
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

        ];
    }

    public function messages(): array
    {
        return [

            'class_id.required' => 'Please select a class.',

            'section_id.required' => 'Please select a section.',

            'first_name.required' => 'First name is required.',

            'father_name.required' => 'Father name is required.',

            'admission_date.required' => 'Admission date is required.',

            'gender.required' => 'Gender is required.',

            'photo.image' => 'Please upload a valid image.',

            'photo.max' => 'Photo size must not exceed 2 MB.',

        ];
    }
}