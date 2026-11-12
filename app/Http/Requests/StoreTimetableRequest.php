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

    protected function prepareForValidation(): void
    {
        $start = $this->start_time;
        $end = $this->end_time;

        if ($start && strlen($start) >= 8) {
            $start = substr($start, 0, 5);
        }
        if ($end && strlen($end) >= 8) {
            $end = substr($end, 0, 5);
        }

        $this->merge([
            'start_time' => $start,
            'end_time'   => $end,
            'status'     => filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);
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