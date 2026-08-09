<?php

namespace App\Http\Requests\ExamSchedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamScheduleRequest extends FormRequest
{
    /**
     * Authorize
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules
     */
    public function rules(): array
    {
        return [

            'exam_id' => [
                'required',
                'exists:exams,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'teacher_id' => [
                'nullable',
                'exists:teachers,id',
            ],

            'exam_date' => [
                'required',
                'date',
            ],

            'start_time' => [
                'required',
                /* 'date_format:H:i', */
            ],

            'end_time' => [
                'required',
                /* 'date_format:H:i', */
                'after:start_time',
            ],

            'room_no' => [
                'nullable',
                'string',
                'max:50',
            ],

            'full_marks' => [
                'required',
                'numeric',
                'min:1',
            ],

            'pass_marks' => [
                'required',
                'numeric',
                'min:0',
                'lte:full_marks',
            ],

            'instructions' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'status' => [
                'required',
                'boolean',
            ],

        ];
    }

    /**
     * Prepare Validation
     */
    protected function prepareForValidation()
    {
        $this->merge([

            'status' => filter_var(
                $this->status,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? true,

        ]);
    }

    /**
     * Messages
     */
    public function messages(): array
    {
        return [

            'exam_id.required' => 'Exam is required.',

            'class_id.required' => 'Class is required.',

            'section_id.required' => 'Section is required.',

            'subject_id.required' => 'Subject is required.',

            'exam_date.required' => 'Exam Date is required.',

            'start_time.required' => 'Start Time is required.',

            'end_time.required' => 'End Time is required.',

            'end_time.after' => 'End Time must be after Start Time.',

            'full_marks.required' => 'Full Marks is required.',

            'pass_marks.required' => 'Pass Marks is required.',

            'pass_marks.lte' => 'Pass Marks cannot exceed Full Marks.',

        ];
    }
}