<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
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
        $schoolId = $this->input('school_id') ?: (auth()->user()?->school_id ?: 1);

        return [
            'school_id'   => 'nullable|integer|exists:schools,id',
            'name'        => 'required|string|max:255',
            'code'        => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->where('school_id', $schoolId),
            ],
            'type'        => 'required|in:Theory,Practical,Both',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ];
    }
}
