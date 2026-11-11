<?php

namespace App\Repositories\v1\Mobile\Calificar;

use App\Models\Calificacion;
use App\Models\Institucion;
use App\Models\Trabajador;
use Illuminate\Support\Collection;

interface CalificarRepositoryInterface
{
    /** @return Collection<int, Institucion> */
    public function listarInstituciones(?string $buscar, ?string $grupo): Collection;

    /** @return Collection<int, Trabajador> */
    public function listarTrabajadores(Institucion $institucion, ?string $buscar): Collection;

    public function cargarTrabajador(Trabajador $trabajador): Trabajador;

    /**
     * Búsqueda de trabajadores en todas las instituciones.
     *
     * @return Collection<int, Trabajador>
     */
    public function buscarTrabajadores(?string $buscar, int $limite): Collection;

    /**
     * @return array{promedio: float|null, total: int}
     */
    public function estadisticasTrabajador(int $trabajadorId): array;

    /**
     * Últimos comentarios públicos (solo texto + fecha).
     *
     * @return Collection<int, Calificacion>
     */
    public function comentariosTrabajador(int $trabajadorId, int $limite): Collection;

    /**
     * Ranking: promedio y total por trabajador desde una fecha.
     *
     * @return Collection<int, object{trabajador_id: int, promedio: float, total: int}>
     */
    public function ranking(\DateTimeInterface $desde, int $limite): Collection;

    /** @return Collection<int, Trabajador> */
    public function trabajadoresPorIds(array $ids): Collection;

    /** @return Collection<int, Calificacion> */
    public function calificacionesDeUsuario(int $userId, int $limite): Collection;

    /**
     * IDs (de la lista dada) que el usuario ya calificó hoy.
     *
     * @param  array<int>  $trabajadorIds
     * @return array<int>
     */
    public function trabajadoresCalificadosHoy(int $userId, array $trabajadorIds): array;

    public function existeCodigo(string $codigo): bool;

    /**
     * @param  array<int, array{criterio_id: int, puntaje: int}>  $detalles
     */
    public function crearCalificacion(array $data, array $detalles): Calificacion;
}
