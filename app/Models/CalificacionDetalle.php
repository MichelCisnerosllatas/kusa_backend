<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('calificacion_detalles')]
#[Fillable(['calificacion_id', 'criterio_id', 'puntaje'])]
class CalificacionDetalle extends Model
{
    public function calificacion(): BelongsTo
    {
        return $this->belongsTo(Calificacion::class);
    }

    public function criterio(): BelongsTo
    {
        return $this->belongsTo(Criterio::class);
    }
}
