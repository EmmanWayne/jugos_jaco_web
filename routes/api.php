<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'getClients']);
        Route::post('/', [ClientController::class, 'createClient']);
        Route::put('/{id}', [ClientController::class, 'updateClient']);
    });
});
