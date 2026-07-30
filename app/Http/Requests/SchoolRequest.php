<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolRequest extends FormRequest
{
    public function authorize()
    {
        // Use your permission system (e.g., Spatie)
        return $this->user()->can('school.create') || $this->user()->can('school.update');
    }

    public function rules()
    {
        $schoolId = $this->route('school')?->id ?? null;

        return [
            'name'         => 'required|string|max:255',
            'code'         => 'required|string|max:255|unique:schools,code,' . $schoolId,
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string',
            'city'         => 'nullable|string|max:255',
            'state'        => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:255',
            'postal_code'  => 'nullable|string|max:20',
            'logo'         => 'nullable|string|max:255', // or file validation
            'status'       => 'boolean',
        ];
    }
}