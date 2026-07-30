<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimetableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],

            'section_id' => ['required', 'exists:sections,id'],

            'subject_id' => ['required', 'exists:subjects,id'],

            'teacher_id' => ['required', 'exists:teachers,id'],

            'day' => [
                'required',
                'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'
            ],

            'start_time' => ['required', 'date_format:H:i'],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time'
            ],

            'room' => [
                'nullable',
                'string',
                'max:50'
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
            'class_id' => 'Class',
            'section_id' => 'Section',
            'subject_id' => 'Subject',
            'teacher_id' => 'Teacher',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
        ];
    }
}