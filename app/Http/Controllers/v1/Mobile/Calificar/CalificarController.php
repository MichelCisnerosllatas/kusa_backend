<?php

namespace App\Http\Controllers\v1\Mobile\Calificar;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Mobile\Calificar\StoreCalificacionRequest;
use App\Http\Resources\v1\Mobile\Calificar\CalificacionResource;
use App\Http\Resources\v1\Mobile\Calificar\InstitucionResource;
use App\Http\Resources\v1\Mobile\Calificar\TrabajadorResource;
use App\Models\Institucion;
use App\Models\Trabajador;
use App\Response\ApiResponse;
use App\Services\v1\Mobile\Calificar\CalificarServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * App móvil · Calificar sin QR.
 */
class CalificarController extends Controller
{
    public function __construct(
        private CalificarServices $calificarServices,
    ) {}

    /**
     * GET /api/v1/mobile/instituciones?buscar=&grupo=&latitud=&longitud=
     */
    public function instituciones(Request $request): JsonResponse
    {
        $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'grupo' => ['nullable', 'string', 'max:100'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitud'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitud'],
        ]);

        $instituciones = $this->calificarServices->instituciones(
            $request->string('buscar')->trim()->value() ?: null,
            $request->string('grupo')->trim()->value() ?: null,
            $request->filled('latitud') ? (float) $request->input('latitud') : null,
            $request->filled('longitud') ? (float) $request->input('longitud') : null,
        );

        return ApiResponse::success(InstitucionResource::collection($instituciones), 'Instituciones');
    }

    /**
     * GET /api/v1/mobile/instituciones/{institucion}/trabajadores?buscar=
     */
    public function trabajadores(Request $request, Institucion $institucion): JsonResponse
    {
        $request->validate(['buscar' => ['nullable', 'string', 'max:100']]);

        $trabajadores = $this->calificarServices->trabajadores(
            $request->user(),
            $institucion,
            $request->string('buscar')->trim()->value() ?: null,
        );

        return ApiResponse::success(TrabajadorResource::collection($trabajadores), 'Trabajadores');
    }

    /**
     * GET /api/v1/mobile/trabajadores?buscar=
     * Buscador global (todas las instituciones).
     */
    public function buscarTrabajadores(Request $request): JsonResponse
    {
        $request->validate(['buscar' => ['nullable', 'string', 'max:100']]);

        $trabajadores = $this->calificarServices->buscarTrabajadores(
            $request->user(),
            $request->string('buscar')->trim()->value() ?: null,
        );

        return ApiResponse::success(TrabajadorResource::collection($trabajadores), 'Trabajadores');
    }

    /**
     * GET /api/v1/mobile/ranking?periodo=hoy|semana|mes
     */
    public function ranking(Request $request): JsonResponse
    {
        $request->validate(['periodo' => ['nullable', Rule::in(CalificarServices::PERIODOS)]]);

        return ApiResponse::success(
            $this->calificarServices->ranking($request->input('periodo', 'hoy')),
            'Ranking',
        );
    }

    /**
     * GET /api/v1/mobile/mis-calificaciones
     */
    public function misCalificaciones(Request $request): JsonResponse
    {
        return ApiResponse::success(
            CalificacionResource::collection($this->calificarServices->misCalificaciones($request->user())),
            'Mis calificaciones',
        );
    }

    /**
     * GET /api/v1/mobile/trabajadores/{trabajador}
     * Devuelve el trabajador + su institución con los criterios a calificar
     * + resumen público (promedio, total, comentarios).
     */
    public function trabajador(Request $request, Trabajador $trabajador): JsonResponse
    {
        $trabajador = $this->calificarServices->trabajador($request->user(), $trabajador);

        return ApiResponse::success(new TrabajadorResource($trabajador), 'Trabajador');
    }

    /**
     * POST /api/v1/mobile/calificaciones
     */
    public function store(StoreCalificacionRequest $request): JsonResponse
    {
        $calificacion = $this->calificarServices->calificar($request->user(), $request->validated());

        return ApiResponse::success(
            new CalificacionResource($calificacion),
            'Calificación registrada',
            Response::HTTP_CREATED,
        );
    }
}
