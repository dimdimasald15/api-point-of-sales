<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


abstract class UserController extends Controller
{
    protected function createUser(array $data, string $role): User
    {
        $data_user = [
            "firstname" => $data["firstname"],
            "lastname" => $data["lastname"],
            "phone_number" => $data["phone_number"],
            "email" => $data["email"],
            "address" => $data["address"],
            "city" => $data["city"],
            "province" => $data["province"],
            "country" => $data["country"],
            "postal_code" => $data["postal_code"],
            "role" => $role,
            "comments" => $data["comments"]
        ];

        return User::create($data_user);
    }

    protected function updateUser(array $data, string $idUser)
    {
        $attributes = [
            'firstname',
            'lastname',
            'phone_number',
            'email',
            'address',
            'postal_code',
            'city',
            'province',
            'country',
            'comments'
        ];

        $data_user = User::find($idUser);
        foreach ($attributes as $attribute) {
            if (isset($data[$attribute])) {
                $data_user->{$attribute} = $data[$attribute];
            }
        }
        $data_user->save();
    }

    protected function handleException(\Exception $e): JsonResponse
    {
        DB::rollBack();
        return response()->json([
            "errors" => [
                "message" => [
                    $e->getMessage()
                ]
            ]
        ], 500);
    }

    protected function abortIfUserExists(array $data, string $field, string $message): void
    {
        $model = $field === "username" ? Employee::class : User::class;

        if ($model::where($field, $data[$field])->exists()) {
            abort(400, json_encode([
                "errors" => [
                    $field => [$message]
                ]
            ]));
        }
    }
    protected function abortIfUpdateUserExists(array $data, string $field, string $message, string $idUser): void
    {
        if (isset($data['email']) && User::where('email', $data['email'])->where('id', '!=', $idUser)->exists()) {
            abort(400, json_encode([
                "errors" => [
                    $field => [$message]
                ]
            ]));
        }
    }
}
