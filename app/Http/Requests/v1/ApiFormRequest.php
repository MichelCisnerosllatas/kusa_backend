<?php

namespace App\Http\Requests\v1;

use App\Response\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Clase base para todos los Form Requests de la API.
 * Devuelve los errores 422 con el formato de ApiResponse.
 * Cada Request hijo puede sobrescribir message() para un mensaje propio.
 */
abstract class ApiFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ApiResponse::error(
                message: $this->message(),
                status: 422,
                errors: $validator->errors()->toArray(),
            )
        );
    }

    protected function message(): string
    {
        return 'Errores de validación';
    }
}
