<?php

namespace App\Http\Requests\StudentAttendance;

use Illuminate\Foundation\Http\FormRequest;

class BulkStudentAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'attendance_date' => [
                'required',
                'date',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'records' => [
                'required',
                'array',
                'min:1',
            ],

            'records.*.student_id' => [
                'required',
                'exists:students,id',
            ],

            'records.*.status' => [
                'required',
                'in:Present,Absent,Late,Half Day,Leave',
            ],

            'records.*.remarks' => [
                'nullable',
                'string',
                'max:500',
            ],

            'records.*.check_in' => [
                'nullable',
                'date_format:H:i',
            ],

            'records.*.check_out' => [
                'nullable',
                'date_format:H:i',
                'after:records.*.check_in',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'records.required' => 'Please mark attendance for at least one student.',

            'records.*.student_id.required' => 'Student is required.',

            'records.*.status.required' => 'Attendance status is required.',

        ];
    }
}