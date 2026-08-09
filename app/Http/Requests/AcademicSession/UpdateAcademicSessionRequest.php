<?php

namespace App\Http\Requests\AcademicSession;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateAcademicSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */

public function rules(): array
{
    $academicSession = $this->route('academic_session');

    return [

        'school_id' => ['required', 'exists:schools,id'],

        'name' => [

            'required',

            'string',

            'max:100',

            Rule::unique('academic_sessions', 'name')
                ->ignore($academicSession)
                ->where(fn ($q) =>
                    $q->where('school_id', $this->school_id)
                ),

        ],

        'start_date' => ['required', 'date'],

        'end_date' => ['required', 'date', 'after:start_date'],

        'status' => ['required', 'boolean'],

        'is_current' => ['required', 'boolean'],

    ];
}
}
