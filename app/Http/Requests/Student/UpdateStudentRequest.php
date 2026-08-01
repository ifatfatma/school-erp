<?php

namespace App\Http\Requests\Student;

use Illuminate\Validation\Rule;

class UpdateStudentRequest extends StoreStudentRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $studentId = $this->route('student')->id ?? $this->route('student');

        $rules['admission_no'] = [
            'nullable',
            'string',
            'max:50',
            Rule::unique('students', 'admission_no')->ignore($studentId),
        ];

        return $rules;
    }
}