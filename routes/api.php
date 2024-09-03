<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/users/login', [EmployeeController::class, 'login']);

Route::middleware(ApiAuthMiddleware::class)->group(function () {
    Route::get('/users', [EmployeeController::class, 'list']);
    Route::post('/users', [EmployeeController::class, 'create']);
    Route::get('/users/current', [EmployeeController::class, 'get']);
    Route::patch('/users/current', [EmployeeController::class, 'update']);
    Route::delete('/users/logout', [EmployeeController::class, 'logout']);

    Route::get('/customers', [CustomerController::class, 'list']);
    Route::post('/customers', [CustomerController::class, 'create']);
    Route::get('/customers/{idCustomer}', [CustomerController::class, 'detail']);
    Route::patch('/customers/{idCustomer}', [CustomerController::class, 'update']);

    Route::get('/suppliers', [SupplierController::class, 'list']);
    Route::post('/suppliers', [SupplierController::class, 'create']);
    Route::get('/suppliers/{idSupplier}', [SupplierController::class, 'detail']);
    Route::patch('/suppliers/{idSupplier}', [SupplierController::class, 'update']);
});
