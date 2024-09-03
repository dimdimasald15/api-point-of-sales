<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierCreateRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends UserController
{
    public function list(): JsonResponse
    {
        $users = Supplier::all();
        return (SupplierResource::collection($users))->response()->setStatusCode(200);
    }

    public function create(SupplierCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->abortIfUserExists($data, 'email', 'Email already registered');

        DB::beginTransaction();
        try {
            $user = $this->createUser($data, 'supplier');
            $data_supplier = [
                "user_id" => $user->id,
                "account_number" => $data['account_number'],
            ];

            Supplier::create($data_supplier);
            DB::commit();

            return (new SupplierResource(array_merge($data, $data_supplier, ['id' => $user->id])))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function detail(Request $request): SupplierResource
    {
        $supplier = Supplier::where('user_id', $request->idSupplier)->first();
        return new SupplierResource($supplier);
    }

    public function update(SupplierUpdateRequest $request): SupplierResource
    {
        $data = $request->validated();
        $supplier = Supplier::where('user_id', $request->idSupplier)->first();
        $this->abortIfUpdateUserExists($data, 'email', 'Email already registered', $request->idSupplier);
        $this->updateUser($data, $request->idSupplier);
        if (isset($data['account_number'])) {
            $supplier->account_number = $data['account_number'];
        }
        if (isset($data['taxable'])) {
            $supplier->taxable = $data['taxable'];
        }
        return new SupplierResource($supplier);
    }
}
