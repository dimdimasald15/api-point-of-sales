<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Requests\EmployeeLoginRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends UserController
{
    public function create(EmployeeCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->abortIfUserExists($data, 'email', 'Email already registered');
        $this->abortIfUserExists($data, 'username', 'Username already registered');

        DB::beginTransaction();
        try {
            $user = $this->createUser($data, 'employee');
            $data_employee = [
                "user_id" => $user->id,
                "username" => $data['username'],
                "password" => Hash::make($data['password']),
                "token" => null,
            ];

            Employee::create($data_employee);
            DB::commit();
            $dataResource = json_decode(json_encode(array_merge($data, $data_employee, ['id' => $user->id])));
            return (new EmployeeResource($dataResource))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(EmployeeUpdateRequest $request): EmployeeResource
    {
        $data = $request->validated();
        $employee = Auth::user();
        $this->abortIfUpdateUserExists($data, 'email', 'Email already registered', $employee->user_id);
        // update data user
        $this->updateUser($data, $employee->user_id);
        // update data employee
        if (isset($data['username'])) {
            $employee->username = $data['username'];
        }
        if (isset($data['password'])) {
            $employee->password = Hash::make($data['password']);
        }
        $employee->save();
        $data_resource = $this->getDataEmployee(Auth::user());
        return new EmployeeResource(json_decode(json_encode($data_resource)));
    }

    public function Login(EmployeeLoginRequest $request): EmployeeResource
    {
        $data = $request->validated();
        $employee = Employee::where('username', $data['username'])->first();
        if (!$employee || !Hash::check($data['password'], $employee->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Username or password wrong",
                    ]
                ]
            ], 401));
        }
        // simpan token
        $employee->token = Str::uuid()->toString();
        $employee->save();
        $user = $employee->user;
        $data = [
            'id' => $employee->id,
            'user_id' => $employee->user_id,
            'username' => $employee->username,
            'token' => $employee->token,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
        ];
        return new EmployeeResource(json_decode(json_encode($data)));
    }

    private function getDataEmployee(object $employee)
    {
        return [
            'id' => $employee->id,
            'user_id' => $employee->user_id,
            'username' => $employee->username,
            'token' => $employee->token,
            'firstname' => $employee->user->firstname,
            'lastname' => $employee->user->lastname,
            'email' => $employee->user->email,
        ];
    }

    public function get(Request $request): EmployeeResource
    {
        $data_resource = $this->getDataEmployee(Auth::user());
        return new EmployeeResource(json_decode(json_encode($data_resource)));
    }

    public function logout(Request $request): JsonResponse
    {
        $employee = Auth::user();
        $employee->token = null;

        $employee->save();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
