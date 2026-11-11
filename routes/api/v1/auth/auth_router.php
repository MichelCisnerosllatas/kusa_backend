<?php

use App\Http\Controllers\v1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
| Autenticación compartida por intranet, landing y mobile.
| "throttle:auth" = límite definido en AppServiceProvider (intentos por minuto por IP).
*/

Route::prefix('auth')->group(function () {
    // Públicas
    // Login solo con DNI. El registro es automático dentro del login.
    Route::middleware('throttle:auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    // Requieren token (Authorization: Bearer xxx)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
    });
});
