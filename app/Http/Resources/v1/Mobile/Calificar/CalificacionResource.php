<?php

namespace App\Http\Resources\v1\Mobile\Calificar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Calificacion
 */
class CalificacionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'promedio' => $this->promedio,
            'comentario' => $this->comentario,
            'trabajador' => $this->whenLoaded('trabajador', fn () => [
                'id' => $this->trabajador->id,
                'nombre_completo' => $this->trabajador->nombre_completo,
                'nombres' => $this->trabajador->nombres,
                'apellidos' => $this->trabajador->apellidos,
                'cargo' => $this->trabajador->cargo,
                'foto_url' => $this->trabajador->foto_url,
            ]),
            'institucion' => $this->whenLoaded('institucion', fn () => [
                'id' => $this->institucion->id,
                'nombre' => $this->institucion->nombre,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
