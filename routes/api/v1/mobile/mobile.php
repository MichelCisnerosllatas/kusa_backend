<?php

use Illuminate\Support\Facades\Route;

/*
| Aplicación móvil.
| Cada módulo va en su propio archivo: routes/api/v1/mobile/{modulo}/{modulo}_router.php
*/

Route::prefix('mobile')->middleware('auth:sanctum')->group(function () {
    require base_path('routes/api/v1/mobile/calificar/calificar_router.php');
});
