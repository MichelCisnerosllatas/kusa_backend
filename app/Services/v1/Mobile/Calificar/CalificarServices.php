<?php

namespace App\Services\v1\Mobile\Calificar;

use App\Exceptions\ApiException;
use App\Models\Calificacion;
use App\Models\Institucion;
use App\Models\Trabajador;
use App\Models\User;
use App\Repositories\v1\Mobile\Calificar\CalificarRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Flujo "Calificar sin QR":
 *  1. Elegir institución (búsqueda + distancia opcional).
 *  2. Elegir trabajador de esa institución.
 *  3. Calificar cada criterio (1-5) + comentario opcional.
 */
class CalificarServices
{
    /**
     * Mínimo de calificaciones para mostrar promedio y comentarios de un
     * trabajador. Con menos, se podría adivinar quién calificó (prototipo: KMIN).
     */
    public const MINIMO_PUBLICO = 5;

    /** Periodos del ranking => días hacia atrás (hoy = desde las 00:00). */
    public const PERIODOS = ['hoy', 'semana', 'mes'];

    public function __construct(
        private CalificarRepositoryInterface $repository,
    ) {}

    /**
     * Si llegan latitud/longitud se calcula la distancia y se ordena por cercanía.
     *
     * @return Collection<int, Institucion>
     */
    public function instituciones(?string $buscar, ?string $grupo, ?float $lat, ?float $lng): Collection
    {
        $instituciones = $this->repository->listarInstituciones($buscar, $grupo);

        if ($lat === null || $lng === null) {
            return $instituciones;
        }

        return $instituciones
            ->each(function (Institucion $i) use ($lat, $lng) {
                $i->distancia_m = ($i->latitud !== null && $i->longitud !== null)
                    ? (int) round($this->distanciaMetros($lat, $lng, $i->latitud, $i->longitud))
                    : null;
            })
            ->sortBy(fn (Institucion $i) => $i->distancia_m ?? PHP_INT_MAX)
            ->values();
    }

    /**
     * @return Collection<int, Trabajador>
     */
    public function trabajadores(User $user, Institucion $institucion, ?string $buscar): Collection
    {
        $trabajadores = $this->repository->listarTrabajadores($institucion, $buscar);
        $this->marcarCalificadosHoy($user, $trabajadores);

        return $trabajadores;
    }

    /**
     * Trabajador con su institución y los criterios a calificar.
     *
     * @throws ApiException
     */
    public function trabajador(User $user, Trabajador $trabajador): Trabajador
    {
        if (! $trabajador->activo) {
            throw new ApiException('El trabajador no está disponible', Response::HTTP_NOT_FOUND);
        }

        $trabajador = $this->repository->cargarTrabajador($trabajador);
        $this->marcarCalificadosHoy($user, collect([$trabajador]));
        $trabajador->resumen = $this->resumen($trabajador->id);

        return $trabajador;
    }

    /**
     * Buscador global de trabajadores (pestaña "Buscar").
     *
     * @return Collection<int, Trabajador>
     */
    public function buscarTrabajadores(User $user, ?string $buscar): Collection
    {
        $trabajadores = $this->repository->buscarTrabajadores($buscar, 50);
        $this->marcarCalificadosHoy($user, $trabajadores);

        return $trabajadores;
    }

    /**
     * Ranking del periodo: hoy (desde las 00:00), semana (últimos 7 días)
     * o mes (últimos 30 días).
     *
     * @return array<int, array<string, mixed>>
     */
    public function ranking(string $periodo): array
    {
        $desde = match ($periodo) {
            'semana' => now()->subDays(6)->startOfDay(),
            'mes' => now()->subDays(29)->startOfDay(),
            default => now()->startOfDay(),
        };

        $filas = $this->repository->ranking($desde, 50);
        $trabajadores = $this->repository->trabajadoresPorIds($filas->pluck('trabajador_id')->all())->keyBy('id');

        return $filas->values()->map(function ($fila, $i) use ($trabajadores) {
            $t = $trabajadores[$fila->trabajador_id] ?? null;

            return $t ? [
                'posicion' => $i + 1,
                'promedio' => round((float) $fila->promedio, 2),
                'total' => (int) $fila->total,
                'trabajador' => [
                    'id' => $t->id,
                    'nombre_completo' => $t->nombre_completo,
                    'nombres' => $t->nombres,
                    'apellidos' => $t->apellidos,
                    'cargo' => $t->cargo,
                    'foto_url' => $t->foto_url,
                    'institucion_id' => $t->institucion_id,
                    'institucion_nombre' => $t->institucion?->nombre,
                ],
            ] : null;
        })->filter()->values()->all();
    }

    /**
     * @return Collection<int, Calificacion>
     */
    public function misCalificaciones(User $user): Collection
    {
        return $this->repository->calificacionesDeUsuario($user->id, 100);
    }

    /**
     * Resumen público: promedio, total y comentarios (solo fecha, sin hora
     * ni autor). Si hay menos de MINIMO_PUBLICO calificaciones, se protege.
     */
    private function resumen(int $trabajadorId): array
    {
        $stats = $this->repository->estadisticasTrabajador($trabajadorId);
        $protegido = $stats['total'] < self::MINIMO_PUBLICO;

        return [
            'total' => $stats['total'],
            'minimo' => self::MINIMO_PUBLICO,
            'protegido' => $protegido,
            'promedio' => $protegido ? null : $stats['promedio'],
            'comentarios' => $protegido ? [] : $this->repository
                ->comentariosTrabajador($trabajadorId, 20)
                ->map(fn ($c) => ['texto' => $c->comentario, 'fecha' => $c->created_at->toDateString()])
                ->all(),
        ];
    }

    /**
     * @throws ApiException
     */
    public function calificar(User $user, array $data): Calificacion
    {
        $trabajador = $this->repository->cargarTrabajador(Trabajador::findOrFail($data['trabajador_id']));

        if (! $trabajador->activo) {
            throw new ApiException('El trabajador no está disponible', Response::HTTP_NOT_FOUND);
        }

        // Regla del prototipo: 1 calificación por trabajador al día.
        if ($this->repository->trabajadoresCalificadosHoy($user->id, [$trabajador->id])) {
            throw new ApiException('Ya calificaste a esta persona hoy. Mañana podrás volver a hacerlo.', Response::HTTP_CONFLICT);
        }

        // Deben venir TODOS los criterios de la institución, y solo esos.
        $criterioIds = $trabajador->institucion->criterios->pluck('id')->sort()->values()->all();
        $puntajes = collect($data['puntajes'])->keyBy('criterio_id');
        $enviados = $puntajes->keys()->map(fn ($id) => (int) $id)->sort()->values()->all();

        if ($criterioIds !== $enviados) {
            throw new ApiException(
                'Debes calificar todos los criterios',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ['puntajes' => ['Los criterios enviados no coinciden con los de la institución.']],
            );
        }

        $detalles = $puntajes->map(fn ($p) => [
            'criterio_id' => (int) $p['criterio_id'],
            'puntaje' => (int) $p['puntaje'],
        ])->values()->all();

        $comentario = isset($data['comentario']) ? trim($data['comentario']) : null;

        return $this->repository->crearCalificacion([
            'codigo' => $this->generarCodigo(),
            'user_id' => $user->id,
            'trabajador_id' => $trabajador->id,
            'institucion_id' => $trabajador->institucion_id,
            'promedio' => round(collect($detalles)->avg('puntaje'), 2),
            'comentario' => $comentario !== '' ? $comentario : null,
            'latitud' => $data['latitud'] ?? null,
            'longitud' => $data['longitud'] ?? null,
        ], $detalles);
    }

    /**
     * Agrega el atributo calificado_hoy a cada trabajador.
     *
     * @param  Collection<int, Trabajador>  $trabajadores
     */
    private function marcarCalificadosHoy(User $user, Collection $trabajadores): void
    {
        $hoy = $this->repository->trabajadoresCalificadosHoy($user->id, $trabajadores->pluck('id')->all());
        $trabajadores->each(fn (Trabajador $t) => $t->calificado_hoy = in_array($t->id, $hoy, true));
    }

    private function generarCodigo(): string
    {
        do {
            $codigo = 'KU-'.Str::upper(Str::random(6));
        } while ($this->repository->existeCodigo($codigo));

        return $codigo;
    }

    /** Fórmula de Haversine (igual que en el prototipo). */
    private function distanciaMetros(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $r * asin(sqrt($a));
    }
}
