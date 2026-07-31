<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherAttendanceRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'teacher_id' => [
                'required',
                'exists:teachers,id',
            ],

            'attendance_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Present',
                    'Absent',
                    'Late',
                    'Half Day',
                    'Leave',
                ]),
            ],

            'check_in' => [
                'nullable',
                'date_format:H:i',
            ],

            'check_out' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:check_in',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'teacher_id.required' => 'Please select a teacher.',
            'teacher_id.exists' => 'Selected teacher does not exist.',

            'attendance_date.required' => 'Attendance date is required.',
            'attendance_date.date' => 'Please provide a valid attendance date.',

            'status.required' => 'Attendance status is required.',
            'status.in' => 'Invalid attendance status selected.',

            'check_in.date_format' => 'Check In time must be in HH:MM format.',
            'check_out.date_format' => 'Check Out time must be in HH:MM format.',
            'check_out.after_or_equal' => 'Check Out time must be after or equal to Check In time.',

            'remarks.max' => 'Remarks may not be greater than 1000 characters.',
        ];
    }
}