<?php

use App\Http\Controllers\v1\Mobile\Calificar\CalificarController;
use Illuminate\Support\Facades\Route;

/*
| App móvil · Calificar, buscar y ranking.
| Prefijo: /api/v1/mobile  (requiere token Sanctum)
*/

Route::get('instituciones', [CalificarController::class, 'instituciones']);
Route::get('instituciones/{institucion}/trabajadores', [CalificarController::class, 'trabajadores']);

Route::get('trabajadores', [CalificarController::class, 'buscarTrabajadores']);
Route::get('trabajadores/{trabajador}', [CalificarController::class, 'trabajador']);

Route::get('ranking', [CalificarController::class, 'ranking']);

Route::get('mis-calificaciones', [CalificarController::class, 'misCalificaciones']);
Route::post('calificaciones', [CalificarController::class, 'store']);
