<?php

namespace App\Http\Requests;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subject = $this->route('subject');
        $subjectId = $subject instanceof Subject ? $subject->id : (is_numeric($subject) ? (int)$subject : (is_object($subject) ? $subject->id : $subject));
        $schoolId = $this->input('school_id') ?: ($subject instanceof Subject ? $subject->school_id : (auth()->user()?->school_id ?: 1));

        return [
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],

            'name' => ['required', 'string', 'max:255'],

            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')
                    ->where(fn ($q) => $q->where('school_id', $schoolId))
                    ->ignore($subjectId),
            ],

            'type' => ['required', Rule::in(['Theory', 'Practical', 'Both'])],

            'description' => ['nullable', 'string'],

            'status' => ['required', 'boolean'],
        ];
    }
}
