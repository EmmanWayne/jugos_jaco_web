<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ResourceMediaController;
use App\Http\Controllers\ClientVisitDayController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'getClients']);
        Route::post('/', [ClientController::class, 'createClient']);
        Route::put('/{id}', [ClientController::class, 'updateClient']);
        Route::post('/{id}/image/business', [ClientController::class, 'uploadBusinessImage']);
        Route::get('/{id}/images/business', [ClientController::class, 'getBusinessImages']);
        Route::get('/{id}/image/profile', [ClientController::class, 'getProfileImage']);
        Route::post('/{id}/image/profile', [ClientController::class, 'uploadProfileImage']);
        
        Route::patch('/{client_id}/visit-days/reorder', [ClientVisitDayController::class, 'reorderVisitDays']);
        Route::get('/{client_id}/visit-days', [ClientVisitDayController::class, 'getVisitDays']);
        Route::post('/{client_id}/visit-days', [ClientVisitDayController::class, 'createVisitDay']);
        Route::get('/{client_id}/visit-days/{id}', [ClientVisitDayController::class, 'getVisitDayById']);
        Route::delete('/{client_id}/visit-days/{id}', [ClientVisitDayController::class, 'deleteVisitDay']);
    });

    Route::prefix('media')->group(function () {
        Route::delete('/{id}', [ResourceMediaController::class, 'deleteMedia']);
    });

    Route::prefix('employees')->group(function () {
        Route::get('/{id}', [EmployeeController::class, 'getEmployee']);
        Route::post('/{id}/location', [EmployeeController::class, 'createLocation']);
    });
});
