<?php

namespace App\Services\v1\Dni;

/**
 * Padrón de DNIs FICTICIOS que simula la API pública de DNI.
 *
 * Solo se consulta cuando el DNI NO está en la tabla users. Si aparece aquí,
 * AuthServices registra al usuario automáticamente; la siguiente vez ya entra
 * directo desde la BD.
 *
 * Los 5 primeros también se crean en UserSeeder (ya registrados).
 * Los demás sirven para probar el registro automático.
 */
class PadronFicticioDniProvider implements DniProviderInterface
{
    /** @var array<string, array{0: string, 1: string}> dni => [nombres, apellidos] */
    public const PADRON = [
        // Ya registrados por UserSeeder
        '12345678' => ['Usuario', 'Demo Kusa'],          // DNI fácil para pruebas
        '70000001' => ['Carlos Alberto', 'Ramírez Torres'],
        '70000002' => ['María Fernanda', 'Gonzales Ríos'],
        '70000003' => ['José Luis', 'Panaifo Vela'],
        '70000004' => ['Rosa Elena', 'Tapullima Saavedra'],
        // Solo en el padrón: se registran al primer login
        '70000005' => ['Luis Miguel', 'Flores Huamán'],
        '70000006' => ['Ana Lucía', 'Pérez Sinarahua'],
        '70000007' => ['Jorge Enrique', 'Del Castillo Rengifo'],
        '70000008' => ['Patricia', 'Macedo Isuiza'],
        '70000009' => ['Miguel Ángel', 'Ushiñahua Pinedo'],
        '70000010' => ['Sofía Valeria', 'Chávez Amasifuén'],
    ];

    public function buscar(string $dni): ?array
    {
        if (! isset(self::PADRON[$dni])) {
            return null;
        }

        [$nombres, $apellidos] = self::PADRON[$dni];

        return [
            'dni' => $dni,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'nombre_completo' => "{$nombres} {$apellidos}",
        ];
    }
}
