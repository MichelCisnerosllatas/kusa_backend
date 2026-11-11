<?php

namespace App\Services\v1\Dni;

/**
 * Fuente externa para consultar personas por DNI.
 *
 * Implementaciones:
 *  - PadronFicticioDniProvider → datos de prueba (actual).
 *  - (futuro) ApiPublicaDniProvider → API pública de DNI / RENIEC.
 *
 * Para cambiar de proveedor solo se modifica el bind en AppServiceProvider.
 */
interface DniProviderInterface
{
    /**
     * @return array{dni: string, nombres: string, apellidos: string, nombre_completo: string}|null
     *         null si el DNI no existe.
     */
    public function buscar(string $dni): ?array;
}
