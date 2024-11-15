<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ManagerController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'authenticate']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('companies')->group(function() {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::delete('/{id}', [CompanyController::class, 'destroy']);
    });
    Route::prefix('managers')->group(function() {
        Route::get('/', [CompanyController::class, 'index']);
        Route::get('/{id}', [CompanyController::class, 'show']);
    });
    Route::apiResource('employees', EmployeeController::class);
});
