<?php

namespace App\Http\Requests;

class UpdateTeacherRequest extends StoreTeacherRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // On update, allow existing string path or new uploaded image
        $rules['photo'] = [
            'nullable',
            function ($attribute, $value, $fail) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $fail('The photo must be an image (jpg, jpeg, png, webp).');
                    }
                    if ($value->getSize() > 2048 * 1024) {
                        $fail('The photo size may not be greater than 2048 kilobytes.');
                    }
                }
            }
        ];

        return $rules;
    }
}
