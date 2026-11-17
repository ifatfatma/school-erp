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
        $routeParam = $this->route('academic_session') ?? $this->route('academic-session');
        $sessionModel = $routeParam instanceof \App\Models\AcademicSession
            ? $routeParam 
            : (is_numeric($routeParam) ? \App\Models\AcademicSession::withoutGlobalScopes()->find($routeParam) : null);
        $sessionId = $sessionModel?->id ?? (is_numeric($routeParam) ? (int)$routeParam : null);

        $tenantManager = app(\App\Services\TenantManager::class);
        $schoolId = $this->input('school_id')
            ?: ($sessionModel?->school_id
            ?: ($this->user()?->school_id
            ?: ($tenantManager->getTenantId() ?: 1)));

        return [

            'school_id' => ['nullable', 'exists:schools,id'],

            'name' => [

                'required',

                'string',

                'max:100',

                Rule::unique('academic_sessions', 'name')
                    ->ignore($sessionId)
                    ->where(fn ($q) =>
                        $q->where('school_id', $schoolId)
                    ),

            ],

            'start_date' => ['required', 'date'],

            'end_date' => ['required', 'date', 'after:start_date'],

            'status' => ['required', 'boolean'],

            'is_current' => ['required', 'boolean'],

        ];
    }
}
