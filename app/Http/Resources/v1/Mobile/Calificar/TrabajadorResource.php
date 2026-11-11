<?php

namespace App\Http\Resources\v1\Mobile\Calificar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Trabajador
 */
class TrabajadorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'institucion_id' => $this->institucion_id,
            'titulo' => $this->titulo,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'nombre_completo' => $this->nombre_completo,
            'cargo' => $this->cargo,
            'foto_url' => $this->foto_url,
            'calificado_hoy' => (bool) ($this->calificado_hoy ?? false),
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
            // Solo en el detalle: promedio, total y comentarios públicos.
            'resumen' => $this->when(isset($this->resumen), fn () => $this->resumen),
        ];
    }
}
