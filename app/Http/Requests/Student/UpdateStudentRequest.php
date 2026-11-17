<?php

namespace App\Http\Requests\Student;

use Illuminate\Validation\Rule;

class UpdateStudentRequest extends StoreStudentRequest
{
    protected function prepareForValidation(): void
    {
        // If guardian_mobile is empty or not passed, inherit from mobile or father_mobile
        if (empty($this->guardian_mobile)) {
            $this->merge([
                'guardian_mobile' => $this->father_mobile ?: $this->mobile,
            ]);
        }
        if (empty($this->guardian_name)) {
            $this->merge([
                'guardian_name' => $this->father_name ?: ($this->first_name . ' Guardian'),
            ]);
        }
    }

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

        $rules['guardian_mobile'] = ['nullable', 'string', 'max:20'];
        $rules['father_mobile'] = ['nullable', 'string', 'max:20'];
        $rules['mother_mobile'] = ['nullable', 'string', 'max:20'];
        $rules['mobile'] = ['nullable', 'string', 'max:20'];
        $rules['aadhaar_no'] = ['nullable', 'string', 'max:20'];

        return $rules;
    }
}