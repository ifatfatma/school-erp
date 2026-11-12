<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
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

            'school_id' => [
                'nullable',
                'exists:schools,id',
            ],

            'academic_session_id' => [
                'required',
                'exists:academic_sessions,id',
            ],

            'exam_type_id' => [
                'required',
                'exists:exam_types,id',
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'nullable',
                'string',
                'max:30',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'is_marks_locked' => [
                'nullable',
                'boolean',
            ],

            'is_result_published' => [
                'nullable',
                'boolean',
            ],

            'status' => [
                'required',
                'boolean',
            ],

        ];
    }

    /**
     * Prepare Request
     */
    protected function prepareForValidation()
    {
        $this->merge([

            'status' => filter_var(
                $this->status,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? $this->status,

            'is_marks_locked' => filter_var(
                $this->is_marks_locked,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? false,

            'is_result_published' => filter_var(
                $this->is_result_published,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? false,

        ]);
    }

    /**
     * Messages
     */
    public function messages(): array
    {
        return [

            'academic_session_id.required' =>
                'Academic Session is required.',

            'exam_type_id.required' =>
                'Exam Type is required.',

            'name.required' =>
                'Exam Name is required.',

            'start_date.required' =>
                'Start Date is required.',

            'end_date.required' =>
                'End Date is required.',

            'end_date.after_or_equal' =>
                'End Date must be after or equal to Start Date.',

        ];
    }
}