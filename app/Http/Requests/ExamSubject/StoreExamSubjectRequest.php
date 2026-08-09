<?php

namespace App\Http\Requests\ExamSubject;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamSubjectRequest extends FormRequest
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

            'subject_id' => [
                'required',
                'exists:subjects,id',
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

            'theory_marks' => [
                'required',
                'numeric',
                'min:0',
            ],

            'practical_marks' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'viva_marks' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'project_marks' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'display_order' => [
                'nullable',
                'integer',
                'min:1',
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
    protected function prepareForValidation(): void
    {
        $this->merge([

            'practical_marks' => $this->practical_marks ?? 0,

            'viva_marks' => $this->viva_marks ?? 0,

            'project_marks' => $this->project_marks ?? 0,

            'display_order' => $this->display_order ?? 1,

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

            'exam_id.required' =>
                'Exam is required.',

            'class_id.required' =>
                'Class is required.',

            'subject_id.required' =>
                'Subject is required.',

            'full_marks.required' =>
                'Full Marks is required.',

            'pass_marks.required' =>
                'Pass Marks is required.',

            'pass_marks.lte' =>
                'Pass Marks cannot exceed Full Marks.',

            'theory_marks.required' =>
                'Theory Marks is required.',

        ];
    }
}