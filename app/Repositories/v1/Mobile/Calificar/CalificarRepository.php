<?php

namespace App\Repositories\v1\Mobile\Calificar;

use App\Models\Calificacion;
use App\Models\Institucion;
use App\Models\Trabajador;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CalificarRepository implements CalificarRepositoryInterface
{
    public function listarInstituciones(?string $buscar, ?string $grupo): Collection
    {
        return Institucion::query()
            ->where('activo', true)
            ->when($grupo, fn ($q) => $q->where('grupo', $grupo))
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($w) use ($buscar) {
                    $w->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('tipo', 'like', "%{$buscar}%")
                        ->orWhere('grupo', 'like', "%{$buscar}%")
                        ->orWhere('direccion', 'like', "%{$buscar}%");
                });
            })
            ->withCount(['trabajadores' => fn ($q) => $q->where('activo', true)])
            ->orderBy('nombre')
            ->get();
    }

    public function listarTrabajadores(Institucion $institucion, ?string $buscar): Collection
    {
        return $institucion->trabajadores()
            ->where('activo', true)
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($w) use ($buscar) {
                    $w->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('cargo', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('apellidos')
            ->get();
    }

    public function cargarTrabajador(Trabajador $trabajador): Trabajador
    {
        return $trabajador->load(['institucion.criterios']);
    }

    public function buscarTrabajadores(?string $buscar, int $limite): Collection
    {
        return Trabajador::query()
            ->with('institucion')
            ->where('activo', true)
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($w) use ($buscar) {
                    $w->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('cargo', 'like', "%{$buscar}%")
                        ->orWhereHas('institucion', fn ($i) => $i->where('nombre', 'like', "%{$buscar}%"));
                });
            })
            ->orderBy('apellidos')
            ->limit($limite)
            ->get();
    }

    public function estadisticasTrabajador(int $trabajadorId): array
    {
        $row = Calificacion::query()
            ->where('trabajador_id', $trabajadorId)
            ->selectRaw('AVG(promedio) as promedio, COUNT(*) as total')
            ->first();

        return [
            'promedio' => $row?->total ? round((float) $row->promedio, 2) : null,
            'total' => (int) ($row?->total ?? 0),
        ];
    }

    public function comentariosTrabajador(int $trabajadorId, int $limite): Collection
    {
        return Calificacion::query()
            ->where('trabajador_id', $trabajadorId)
            ->whereNotNull('comentario')
            ->latest()
            ->limit($limite)
            ->get(['id', 'comentario', 'created_at']);
    }

    public function ranking(\DateTimeInterface $desde, int $limite): Collection
    {
        return Calificacion::query()
            ->join('trabajadores', 'trabajadores.id', '=', 'calificaciones.trabajador_id')
            ->where('trabajadores.activo', true)
            ->where('calificaciones.created_at', '>=', $desde)
            ->groupBy('calificaciones.trabajador_id')
            ->selectRaw('calificaciones.trabajador_id, AVG(calificaciones.promedio) as promedio, COUNT(*) as total')
            ->orderByDesc('promedio')
            ->orderByDesc('total')
            ->limit($limite)
            ->toBase()
            ->get();
    }

    public function trabajadoresPorIds(array $ids): Collection
    {
        return Trabajador::with('institucion')->whereIn('id', $ids)->get();
    }

    public function calificacionesDeUsuario(int $userId, int $limite): Collection
    {
        return Calificacion::query()
            ->with(['trabajador', 'institucion'])
            ->where('user_id', $userId)
            ->latest()
            ->limit($limite)
            ->get();
    }

    public function trabajadoresCalificadosHoy(int $userId, array $trabajadorIds): array
    {
        if (empty($trabajadorIds)) {
            return [];
        }

        return Calificacion::query()
            ->where('user_id', $userId)
            ->whereIn('trabajador_id', $trabajadorIds)
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->pluck('trabajador_id')
            ->unique()
            ->values()
            ->all();
    }

    public function existeCodigo(string $codigo): bool
    {
        return Calificacion::where('codigo', $codigo)->exists();
    }

    public function crearCalificacion(array $data, array $detalles): Calificacion
    {
        return DB::transaction(function () use ($data, $detalles) {
            $calificacion = Calificacion::create($data);
            $calificacion->detalles()->createMany($detalles);

            return $calificacion->load(['trabajador', 'institucion']);
        });
    }
}
