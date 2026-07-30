<?php

namespace App\Http\Requests\AcademicSession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'school_id' => ['required', 'integer', 'exists:schools,id'],

            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('academic_sessions')
                    ->where(fn ($query) => $query->where('school_id', $this->school_id)),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'boolean'],
            'is_current' => ['required', 'boolean'],
        ];
    }
}