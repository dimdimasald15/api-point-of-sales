<?php

namespace App\Http\Requests;

class EmployeeCreateRequest extends UserCreateRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'username' => ['required', 'max:100'],
            'password' => ['required'],
        ]);
    }
}
