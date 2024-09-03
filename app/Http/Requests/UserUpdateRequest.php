<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function commonRules(): array
    {
        return [
            'firstname' => ['nullable', 'max:100'],
            'lastname' => ['nullable', 'max:100'],
            'phone_number' => ['nullable', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'max:255'],
            'postal_code' => ['nullable', 'max:20'],
            'city' => ['nullable', 'max:100'],
            'province' => ['nullable', 'max:100'],
            'country' => ['nullable', 'max:100'],
            'comments' => ['nullable'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag(),
        ], 400));
    }
}
