<?php

namespace App\Http\Requests\v1\Mobile\Calificar;

use App\Http\Requests\v1\ApiFormRequest;

/**
 * Body:
 * {
 *   "trabajador_id": 1,
 *   "puntajes": [ { "criterio_id": 1, "puntaje": 5 }, ... ],
 *   "comentario": "opcional",
 *   "latitud": -3.74, "longitud": -73.25   (opcionales)
 * }
 */
class StoreCalificacionRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'trabajador_id' => ['required', 'integer', 'exists:trabajadores,id'],
            'puntajes' => ['required', 'array', 'min:1'],
            'puntajes.*.criterio_id' => ['required', 'integer', 'distinct', 'exists:criterios,id'],
            'puntajes.*.puntaje' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:400'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'trabajador_id.required' => 'Selecciona al trabajador que te atendió.',
            'trabajador_id.exists' => 'El trabajador seleccionado no existe.',
            'puntajes.required' => 'Debes calificar los criterios.',
            'puntajes.*.criterio_id.distinct' => 'Hay criterios repetidos.',
            'puntajes.*.criterio_id.exists' => 'Uno de los criterios no existe.',
            'puntajes.*.puntaje.between' => 'Cada puntaje debe estar entre 1 y 5.',
            'comentario.max' => 'El comentario no debe superar los 400 caracteres.',
        ];
    }

    protected function message(): string
    {
        return 'Error en los datos de la calificación';
    }
}
