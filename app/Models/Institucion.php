<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('instituciones')]
#[Fillable(['codigo', 'nombre', 'tipo', 'grupo', 'direccion', 'telefono', 'correo', 'horario', 'latitud', 'longitud', 'activo'])]
class Institucion extends Model
{
    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'activo' => 'boolean',
        ];
    }

    public function criterios(): HasMany
    {
        return $this->hasMany(Criterio::class)->orderBy('orden');
    }

    public function trabajadores(): HasMany
    {
        return $this->hasMany(Trabajador::class);
    }
}
