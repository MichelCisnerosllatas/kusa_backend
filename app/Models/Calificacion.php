<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('calificaciones')]
#[Fillable(['codigo', 'user_id', 'trabajador_id', 'institucion_id', 'promedio', 'comentario', 'latitud', 'longitud'])]
class Calificacion extends Model
{
    protected function casts(): array
    {
        return [
            'promedio' => 'float',
            'latitud' => 'float',
            'longitud' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CalificacionDetalle::class);
    }
}
