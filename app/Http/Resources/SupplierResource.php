<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'firstname' => $this['firstname'] ? $this['firstname'] : $this->user->firstname,
            'lastname' => $this['lastname'] ? $this['lastname'] : $this->user->lastname,
            'phone_number' => $this['phone_number'] ? $this['phone_number'] : $this->user->phone_number,
            'email' => $this['email'] ? $this['email'] : $this->user->email,
            'role' => "supplier",
            'address' => $this['address'] ? $this['address'] : $this->user->address,
            'city' => $this['city'] ? $this['city'] : $this->user->city,
            'province' => $this['province'] ? $this['province'] : $this->user->province,
            'country' => $this['country'] ? $this['country'] : $this->user->country,
            'postal_code' => $this['postal_code'] ? $this['postal_code'] : $this->user->postal_code,
            'comments' => $this['comments'] ? $this['comments'] : $this->user->comments,
            'user_id' => $this['user_id'],
            'account_number' => $this['account_number'],
        ];
    }
}
