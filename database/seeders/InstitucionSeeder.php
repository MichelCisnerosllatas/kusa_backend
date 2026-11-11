<?php

namespace Database\Seeders;

use App\Models\Institucion;
use Illuminate\Database\Seeder;

/**
 * Instituciones y sus criterios de calificación (tomados del prototipo KUSA).
 * Datos ficticios. Coordenadas en Iquitos / Belén (Loreto).
 */
class InstitucionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->data() as $item) {
            $criterios = $item['criterios'];
            unset($item['criterios']);

            $institucion = Institucion::updateOrCreate(['codigo' => $item['codigo']], $item);

            foreach ($criterios as $orden => [$nombre, $descripcion, $icono]) {
                $institucion->criterios()->updateOrCreate(
                    ['nombre' => $nombre],
                    ['descripcion' => $descripcion, 'icono' => $icono, 'orden' => $orden + 1],
                );
            }
        }
    }

    /**
     * icono: heart | clock | chat | shield | star
     */
    private function data(): array
    {
        return [
            [
                'codigo' => 'hospital',
                'nombre' => 'Hospital Modelo de Maynas',
                'tipo' => 'Hospital',
                'grupo' => 'Centros de Salud',
                'direccion' => 'Av. Modelo 123, Iquitos',
                'telefono' => '(065) 000-000',
                'correo' => 'contacto@hospitalmodelo.example',
                'horario' => 'Atención las 24 horas',
                'latitud' => -3.7437,
                'longitud' => -73.2516,
                'criterios' => [
                    ['Trato y respeto', 'Amabilidad, escucha y respeto', 'heart'],
                    ['Tiempo de espera', 'Puntualidad y duración', 'clock'],
                    ['Claridad de la explicación', 'Explicación clara y sencilla', 'chat'],
                    ['Privacidad en la atención', 'Confidencialidad y confianza', 'shield'],
                ],
            ],
            [
                'codigo' => 'comisaria',
                'nombre' => 'Comisaría Modelo Belén',
                'tipo' => 'Comisaría',
                'grupo' => 'Comisarías',
                'direccion' => 'Jr. Modelo 456, Belén',
                'telefono' => '(065) 000-111',
                'correo' => 'comisaria.belen@policia.example',
                'horario' => 'Atención las 24 horas',
                'latitud' => -3.7563,
                'longitud' => -73.2481,
                'criterios' => [
                    ['Trato respetuoso', 'Amabilidad y respeto', 'heart'],
                    ['Rapidez en la atención', 'Puntualidad y duración', 'clock'],
                    ['Claridad del trámite', 'Explicación clara y sencilla', 'chat'],
                    ['Imparcialidad', 'Trato justo e igual para todos', 'shield'],
                ],
            ],
            [
                'codigo' => 'colegio',
                'nombre' => 'I.E. Modelo San Juan Bosco',
                'tipo' => 'Colegio · UGEL',
                'grupo' => 'Centros Educativos',
                'direccion' => 'Calle Modelo 789, Iquitos',
                'telefono' => '(065) 000-222',
                'correo' => 'secretaria@sanjuanbosco.example',
                'horario' => 'Lunes a viernes, 7:30 a 15:30',
                'latitud' => -3.7712,
                'longitud' => -73.2610,
                'criterios' => [
                    ['Dominio del tema', 'Conocimiento y claridad', 'chat'],
                    ['Trato a los estudiantes', 'Amabilidad y respeto', 'heart'],
                    ['Puntualidad', 'Llegada y cumplimiento del horario', 'clock'],
                    ['Comunicación con las familias', 'Información clara y a tiempo', 'chat'],
                ],
            ],
        ];
    }
}
