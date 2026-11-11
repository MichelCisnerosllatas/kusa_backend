<?php

namespace App\Http\Resources\v1\Mobile\Calificar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Criterio
 */
class CriterioResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'icono' => $this->icono,
            'orden' => $this->orden,
        ];
    }
}
