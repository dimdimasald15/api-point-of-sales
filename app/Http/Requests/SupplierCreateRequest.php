<?php

namespace App\Http\Requests;

class SupplierCreateRequest extends UserCreateRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'account_number' => ['required']
        ]);
    }
}
