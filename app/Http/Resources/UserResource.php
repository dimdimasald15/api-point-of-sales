<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'firstname' => $this['firstname'],
            'lastname' => $this['lastname'],
            'phone_number' => $this['phone_number'],
            'email' => $this['email'],
            'role' => $this['role'],
            'address' => $this['address'],
            'city' => $this['city'],
            'province' => $this['province'],
            'country' => $this['country'],
            'postal_code' => $this['postal_code'],
            'comments' => $this['comments'],
        ];
    }
}
