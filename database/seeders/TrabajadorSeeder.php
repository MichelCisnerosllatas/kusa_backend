<?php

namespace Database\Seeders;

use App\Models\Institucion;
use App\Models\Trabajador;
use Illuminate\Database\Seeder;

/**
 * Personal de cada institución (basado en el prototipo KUSA). Datos ficticios.
 * Requiere InstitucionSeeder.
 */
class TrabajadorSeeder extends Seeder
{
    public function run(): void
    {
        $instituciones = Institucion::pluck('id', 'codigo');

        foreach ($this->data() as $codigo => $trabajadores) {
            $institucionId = $instituciones[$codigo] ?? null;
            if (! $institucionId) {
                continue;
            }

            foreach ($trabajadores as [$titulo, $nombres, $apellidos, $cargo]) {
                Trabajador::updateOrCreate(
                    ['institucion_id' => $institucionId, 'nombres' => $nombres, 'apellidos' => $apellidos],
                    ['titulo' => $titulo, 'cargo' => $cargo, 'activo' => true],
                );
            }
        }
    }

    /**
     * codigo de institución => [titulo, nombres, apellidos, cargo]
     */
    private function data(): array
    {
        return [
            'hospital' => [
                ['Dra.', 'María Elena', 'Pinedo Vásquez', 'Médica de Emergencia'],
                ['Dr.', 'Jorge Luis', 'Arévalo Ruiz', 'Pediatra'],
                ['Obst.', 'Lucía', 'Tuanama Sangama', 'Obstetra'],
                ['Lic.', 'Ricardo', 'Ruiz Del Águila', 'Enfermería de triaje'],
                ['Lic.', 'Carmen Rosa', 'Lozano Tello', 'Admisión y citas'],
            ],
            'comisaria' => [
                ['Of.', 'César Augusto', 'Vásquez Ríos', 'Oficial de turno'],
                ['Sub.', 'Eduardo', 'Mozombite Ahuanari', 'Mesa de denuncias'],
                ['Sub.', 'Andrea', 'Fasabi Tapayuri', 'Mesa de partes'],
                ['S2.', 'Kevin Paul', 'Manihuari Coquinche', 'Atención al ciudadano'],
            ],
            'colegio' => [
                ['Prof.', 'Teresa', 'Cárdenas Ramírez', 'Docente de Matemática'],
                ['Prof.', 'Nilda', 'Shuña Pacaya', 'Docente de 3.° de primaria'],
                ['Prof.', 'Diego', 'Pezo Panduro', 'Docente de Comunicación'],
                ['Lic.', 'Gloria', 'Ríos Flores', 'Dirección académica'],
            ],
        ];
    }
}
