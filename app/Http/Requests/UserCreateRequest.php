<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserCreateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() != null;
    }

    public function commonRules(): array
    {
        return [
            'firstname' => ['required', 'max:100'],
            'lastname' => ['required', 'max:100'],
            'phone_number' => ['required', 'max:20'],
            'email' => ['required', 'email', 'max:100'],
            'address' => ['required', 'max:255'],
            'city' => ['required', 'max:100'],
            'province' => ['required', 'max:100'],
            'country' => ['required', 'max:100'],
            'postal_code' => ['required', 'max:20'],
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
