<?php

namespace App\Http\Requests\Promotion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentPromotionRequest extends FormRequest
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

            /*
            |--------------------------------------------------------------------------
            | Single Promotion
            |--------------------------------------------------------------------------
            */

            'student_id' => [
                'nullable',
                'exists:students,id',
                'required_without:student_ids',
            ],

            /*
            |--------------------------------------------------------------------------
            | Bulk Promotion
            |--------------------------------------------------------------------------
            */

            'student_ids' => [
                'nullable',
                'array',
                'required_without:student_id',
            ],

            'student_ids.*' => [
                'exists:students,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Target Session
            |--------------------------------------------------------------------------
            */

            'to_session_id' => [
                'required',
                'exists:academic_sessions,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Target Class
            |--------------------------------------------------------------------------
            */

            'to_class_id' => [
                'required',
                'exists:classes,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Target Section
            |--------------------------------------------------------------------------
            */

            'to_section_id' => [
                'nullable',
                'exists:sections,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Promotion
            |--------------------------------------------------------------------------
            */

            'promotion_date' => [
                'required',
                'date',
            ],

            'promotion_type' => [

                'required',

                Rule::in([
                    'Promoted',
                    'Repeated',
                    'Transferred',
                    'Graduated',
                    'Dropped',
                ]),

            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ];
    }

    /**
     * Messages
     */
    public function messages(): array
    {
        return [

            'student_id.required_without' =>
                'Please select a student.',

            'student_ids.required_without' =>
                'Please select at least one student.',

            'to_session_id.required' =>
                'Target academic session is required.',

            'to_class_id.required' =>
                'Target class is required.',

            'promotion_date.required' =>
                'Promotion date is required.',

            'promotion_type.required' =>
                'Promotion type is required.',

        ];
    }
}