<?php

use Illuminate\Support\Facades\Route;

/*
| Intranet (panel administrativo).
| Cada módulo va en su propio archivo: routes/api/v1/intranet/{modulo}/{modulo}_router.php
| Ejemplo: require base_path('routes/api/v1/intranet/user/user_router.php');
*/

Route::prefix('intranet')->middleware('auth:sanctum')->group(function () {
    //
});
