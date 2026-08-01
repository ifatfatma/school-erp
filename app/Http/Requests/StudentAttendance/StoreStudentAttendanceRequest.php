<?php

namespace App\Http\Requests\StudentAttendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'school_id' => [
                'required',
                'exists:schools,id',
            ],

            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'attendance_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:Present,Absent,Late,Half Day,Leave',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:500',
            ],

            'check_in' => [
                'nullable',
                'date_format:H:i',
            ],

            'check_out' => [
                'nullable',
                'date_format:H:i',
                'after:check_in',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'student_id.required' => 'Please select a student.',

            'class_id.required' => 'Please select a class.',

            'section_id.required' => 'Please select a section.',

            'attendance_date.required' => 'Attendance date is required.',

            'status.required' => 'Attendance status is required.',

            'check_out.after' => 'Check-out time must be after check-in time.',

        ];
    }
}