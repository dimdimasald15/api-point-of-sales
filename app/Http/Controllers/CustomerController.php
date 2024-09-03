<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CustomerController extends UserController
{
    public function list(): JsonResponse
    {
        $users = Customer::all();
        return (CustomerResource::collection($users))->response()->setStatusCode(200);
    }

    public function create(CustomerCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->abortIfUserExists($data, 'email', 'Email already registered');

        DB::beginTransaction();
        try {
            $user = $this->createUser($data, 'customer');
            $data_customer = [
                "user_id" => $user->id,
                "account_number" => $data['account_number'],
                "taxable" => 1,
            ];

            Customer::create($data_customer);
            DB::commit();

            return (new CustomerResource(array_merge($data, $data_customer, ['id' => $user->id])))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function detail(Request $request): CustomerResource
    {
        $customer = Customer::where('user_id', $request->idCustomer)->first();
        return new CustomerResource($customer);
    }

    public function update(CustomerUpdateRequest $request): CustomerResource|JsonResponse
    {
        $data = $request->validated();

        $customer = Customer::where('user_id', $request->idCustomer)->first();

        if (!$customer) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $this->abortIfUpdateUserExists($data, 'email', 'Email already registered', $request->idCustomer);
        $this->updateUser($data, $request->idCustomer);

        // Update customer fields only if they are present in the request data
        $customer->fill($request->only('account_number', 'taxable'))->save();

        return new CustomerResource($customer);
    }
}
