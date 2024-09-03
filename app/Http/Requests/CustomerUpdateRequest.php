<?php

namespace App\Http\Requests;

class CustomerUpdateRequest extends UserUpdateRequest
{
    public function rules()
    {
        return array_merge($this->commonRules(), [
            'account_number' => ['nullable', 'max:20'],
            'taxable' => ['nullable', 'max:1'],
        ]);
    }
}
