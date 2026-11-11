<?php

namespace App\Http\Resources\v1\Mobile\Calificar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Institucion
 */
class InstitucionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'grupo' => $this->grupo,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'horario' => $this->horario,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'distancia_m' => $this->when(isset($this->distancia_m), fn () => $this->distancia_m),
            'trabajadores_count' => $this->whenCounted('trabajadores'),
            'criterios' => CriterioResource::collection($this->whenLoaded('criterios')),
        ];
    }
}
