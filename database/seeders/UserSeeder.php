<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\v1\Dni\PadronFicticioDniProvider;
use Illuminate\Database\Seeder;

/**
 * Usuarios ya registrados (DNIs ficticios).
 *
 * Se toman los 5 primeros del padrón ficticio (12345678 y 70000001 a 70000004).
 * Los DNIs 70000005 a 70000010 NO se crean aquí: se registran solos en su
 * primer login. Cualquier otro DNI es rechazado.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $registrados = array_slice(PadronFicticioDniProvider::PADRON, 0, 5, preserve_keys: true);

        foreach ($registrados as $dni => [$nombres, $apellidos]) {
            User::updateOrCreate(
                ['dni' => (string) $dni],
                ['name' => "{$nombres} {$apellidos}"],
            );
        }
    }
}
