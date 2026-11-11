<?php

namespace Database\Seeders;

use App\Models\Calificacion;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Calificaciones ficticias de los últimos 30 días (algunas de hoy) para que
 * el ranking y los perfiles no salgan vacíos. Basado en el seed del prototipo.
 *
 * Requiere UserSeeder, InstitucionSeeder y TrabajadorSeeder.
 * Crea 20 "ciudadanos" de prueba (DNI 40000001 a 40000020) que son los que califican.
 */
class CalificacionSeeder extends Seeder
{
    /** Comentarios de ejemplo (tomados del prototipo). */
    private const COMENTARIOS = [
        'Me atendieron rápido y con mucho respeto.',
        'Explicó con paciencia lo que tenía que hacer.',
        'Hubo demora, pero el trato fue amable.',
        'Faltó información clara sobre los pasos del trámite.',
        'Muy buena atención, volvería a venir.',
        'Me hicieron esperar bastante y nadie avisó el motivo.',
        'Resolvió mi consulta en pocos minutos.',
        'Atiende con buena disposición aunque hay mucha gente.',
    ];

    /**
     * Cantidad de calificaciones y "nivel" base (1-5) por trabajador (apellidos).
     * Algunos con menos de 5 para ver los "resultados protegidos".
     */
    private const PERFILES = [
        'Pinedo Vásquez' => [12, 4.5],
        'Arévalo Ruiz' => [9, 4.2],
        'Tuanama Sangama' => [7, 3.8],
        'Ruiz Del Águila' => [10, 3.4],
        'Lozano Tello' => [3, 4.0],
        'Vásquez Ríos' => [8, 3.6],
        'Mozombite Ahuanari' => [6, 3.1],
        'Fasabi Tapayuri' => [8, 2.2],
        'Manihuari Coquinche' => [2, 4.4],
        'Cárdenas Ramírez' => [11, 4.6],
        'Shuña Pacaya' => [9, 4.1],
        'Pezo Panduro' => [7, 3.3],
        'Ríos Flores' => [4, 3.9],
    ];

    public function run(): void
    {
        if (Calificacion::exists()) {
            return; // ya sembrado
        }

        mt_srand(7); // resultados reproducibles

        $ciudadanos = collect(range(1, 20))->map(fn ($n) => User::updateOrCreate(
            ['dni' => sprintf('400000%02d', $n)],
            ['name' => sprintf('Ciudadano de prueba %02d', $n)],
        ));

        $trabajadores = Trabajador::with('institucion.criterios')->get()->keyBy('apellidos');

        foreach (self::PERFILES as $apellidos => [$cantidad, $nivel]) {
            $trabajador = $trabajadores[$apellidos] ?? null;
            if (! $trabajador) {
                continue;
            }

            $criterios = $trabajador->institucion->criterios;
            $usados = []; // "user|día" para respetar 1 calificación por día

            for ($k = 0; $k < $cantidad; $k++) {
                // ~25% hoy, el resto en los últimos 29 días.
                $dias = mt_rand(1, 100) <= 25 ? 0 : mt_rand(1, 29);
                $fecha = now()->subDays($dias)->setTime(mt_rand(7, 18), mt_rand(0, 59));
                if ($fecha->isFuture()) {
                    $fecha = now()->subMinutes(mt_rand(5, 120));
                }

                $user = $ciudadanos[mt_rand(0, $ciudadanos->count() - 1)];
                $clave = $user->id.'|'.$fecha->toDateString();
                if (isset($usados[$clave])) {
                    continue;
                }
                $usados[$clave] = true;

                $detalles = $criterios->map(fn ($c) => [
                    'criterio_id' => $c->id,
                    'puntaje' => max(1, min(5, (int) round($nivel + (mt_rand(-80, 80) / 100)))),
                ])->all();

                $calificacion = new Calificacion([
                    'codigo' => 'KU-'.Str::upper(Str::random(6)),
                    'user_id' => $user->id,
                    'trabajador_id' => $trabajador->id,
                    'institucion_id' => $trabajador->institucion_id,
                    'promedio' => round(collect($detalles)->avg('puntaje'), 2),
                    'comentario' => mt_rand(1, 100) <= 55 ? self::COMENTARIOS[mt_rand(0, count(self::COMENTARIOS) - 1)] : null,
                    'latitud' => $trabajador->institucion->latitud,
                    'longitud' => $trabajador->institucion->longitud,
                ]);
                $calificacion->created_at = $fecha;
                $calificacion->updated_at = $fecha;
                $calificacion->save();

                $calificacion->detalles()->createMany($detalles);
            }
        }
    }
}
