<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
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

            'employee_id' => [
                'required',
                'string',
                'max:50'
            ],

            'first_name' => [
                'required',
                'string',
                'max:100'
            ],

            'last_name' => [
                'nullable',
                'string',
                'max:100'
            ],

            'gender' => [
                'required',
                'in:Male,Female,Other'
            ],

            'date_of_birth' => [
                'nullable',
                'date'
            ],

            'email' => [
                'nullable',
                'email',
                'max:255'
            ],

            'phone' => [
                'required',
                'string',
                'max:20'
            ],

            'qualification' => [
                'nullable',
                'string',
                'max:255'
            ],

            'experience' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'joining_date' => [
                'required',
                'date'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],

            'status' => [
                'required',
                'boolean'
            ],

        ];
    }

    public function attributes(): array
    {
        return [

            'employee_id' => 'Employee ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'date_of_birth' => 'Date of Birth',
            'joining_date' => 'Joining Date',

        ];
    }
}