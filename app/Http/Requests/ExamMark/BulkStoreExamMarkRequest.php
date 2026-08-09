<?php

namespace App\Http\Requests\ExamMark;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreExamMarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'exam_schedule_id' => [
                'required',
                'exists:exam_schedules,id',
            ],

            'marks' => [
                'required',
                'array',
                'min:1',
            ],

            'marks.*.student_id' => [
                'required',
                'exists:students,id',
            ],

            'marks.*.attendance' => [
                'required',
                'in:Present,Absent',
            ],

            'marks.*.theory_marks' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'marks.*.practical_marks' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'marks.*.viva_marks' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'marks.*.project_marks' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'marks.*.remarks' => [
                'nullable',
                'string',
                'max:500',
            ],

        ];
    }

    protected function prepareForValidation()
    {
        $marks = collect($this->marks)->map(function ($row) {

            return [

                'student_id' => $row['student_id'],

                'attendance' => $row['attendance'] ?? 'Present',

                'theory_marks' => $row['theory_marks'] ?? 0,

                'practical_marks' => $row['practical_marks'] ?? 0,

                'viva_marks' => $row['viva_marks'] ?? 0,

                'project_marks' => $row['project_marks'] ?? 0,

                'remarks' => $row['remarks'] ?? null,

            ];

        });

        $this->merge([
            'marks' => $marks->toArray()
        ]);
    }
}