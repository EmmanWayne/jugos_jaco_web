<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ResourceMediaController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'getClients']);
        Route::post('/', [ClientController::class, 'createClient']);
        Route::put('/{id}', [ClientController::class, 'updateClient']);
        Route::post('/{id}/image/business', [ClientController::class, 'uploadBusinessImage']);
        Route::post('/{id}/image/profile', [ClientController::class, 'uploadProfileImage']);
        Route::get('/{id}/images/business', [ClientController::class, 'getImagesBusiness']);
    });

    Route::prefix('media')->group(function () {
        Route::delete('/{id}', [ResourceMediaController::class, 'deleteMedia']);
    });

    Route::prefix('employees')->group(function () {
        Route::get('/{id}', [EmployeeController::class, 'getEmployee']);
        Route::post('/{id}/location', [EmployeeController::class, 'createLocation']);
    });
});
