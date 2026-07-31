<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [

            'teacher_id' => [
                'required',
                'exists:teachers,id'
            ],

            'leave_type_id' => [
                'required',
                'exists:leave_types,id'
            ],

            'from_date' => [
                'required',
                'date'
            ],

            'to_date' => [
                'required',
                'date',
                'after_or_equal:from_date'
            ],

            'reason' => [
                'required',
                'string',
                'max:1000'
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:2048'
            ],

            'status' => [
                'nullable',
                'integer',
                'in:0,1,2,3'
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000'
            ]

        ];
    }

    /**
     * Custom Messages
     */
    public function messages(): array
    {
        return [

            'teacher_id.required' => 'Please select a teacher.',
            'teacher_id.exists' => 'Selected teacher is invalid.',

            'leave_type_id.required' => 'Please select a leave type.',
            'leave_type_id.exists' => 'Selected leave type is invalid.',

            'from_date.required' => 'From date is required.',
            'from_date.date' => 'Invalid from date.',

            'to_date.required' => 'To date is required.',
            'to_date.after_or_equal' => 'To date must be greater than or equal to From date.',

            'reason.required' => 'Reason is required.',

            'attachment.mimes' => 'Allowed files: jpg, jpeg, png, pdf, doc, docx.',
            'attachment.max' => 'Attachment size must not exceed 2 MB.',

        ];
    }
}