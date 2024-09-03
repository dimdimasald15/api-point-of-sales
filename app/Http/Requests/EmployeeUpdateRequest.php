<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends UserUpdateRequest
{
    public function rules()
    {
        return array_merge($this->commonRules(), [
            'username' => ['nullable', 'max:100'],
            'password' => ['nullable', 'max:100'],
        ]);
    }
}
