<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierUpdateRequest extends UserUpdateRequest
{
    public function rules()
    {
        return array_merge($this->commonRules(), [
            'account_number' => ['nullable', 'max:20'],
        ]);
    }
}
