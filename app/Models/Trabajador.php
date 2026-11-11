<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('trabajadores')]
#[Fillable(['institucion_id', 'titulo', 'nombres', 'apellidos', 'cargo', 'foto_url', 'activo'])]
class Trabajador extends Model
{
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Nombre para mostrar: "Dra. María Pinedo Vásquez".
     */
    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(fn () => trim(($this->titulo ? $this->titulo.' ' : '').$this->nombres.' '.$this->apellidos));
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }
}
