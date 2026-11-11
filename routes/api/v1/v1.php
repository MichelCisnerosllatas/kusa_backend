<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
| /api/v1/auth      -> autenticación compartida (intranet, landing y mobile)
| /api/v1/intranet  -> panel administrativo (front intranet)
| /api/v1/landing   -> página pública (front landing)
| /api/v1/mobile    -> aplicación móvil
*/

Route::prefix('v1')->group(function () {
    Route::get('ping', fn () => response()->json(['pong' => true]));

    require base_path('routes/api/v1/auth/auth_router.php');
    require base_path('routes/api/v1/intranet/intranet.php');
    require base_path('routes/api/v1/landing/landing.php');
    require base_path('routes/api/v1/mobile/mobile.php');
});
