<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $schoolId = $this->route('school')?->id ?? $this->route('school');

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
            'logo'         => 'nullable',
            'remove_logo'  => 'nullable|boolean',
            'status'       => 'nullable',
            'subscription_plan'   => 'nullable|string|in:starter,basic,growth,professional,enterprise',
            'subscription_status' => 'nullable|string|in:active,trial,expired,cancelled',
        ];
    }
}
