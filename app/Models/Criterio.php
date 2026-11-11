<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('criterios')]
#[Fillable(['institucion_id', 'nombre', 'descripcion', 'icono', 'orden'])]
class Criterio extends Model
{
    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }
}
