<?php

namespace App\Http\Requests\Guardian;

class UpdateGuardianRequest extends StoreGuardianRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // On update, only validate photo as image if an actual new file is uploaded
        if (!$this->hasFile('photo')) {
            unset($rules['photo']);
        }

        return $rules;
    }
}