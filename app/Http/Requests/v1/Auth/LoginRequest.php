<?php

namespace App\Http\Requests\v1\Auth;

use App\Http\Requests\v1\ApiFormRequest;

/**
 * Login solo con DNI (sin contraseña).
 */
class LoginRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'dni' => ['required', 'string', 'digits:8'],
            'device_name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.digits' => 'El DNI debe tener 8 dígitos.',
            'device_name.required' => 'El nombre del dispositivo es obligatorio.',
            'device_name.max' => 'El nombre del dispositivo no debe superar los 255 caracteres.',
        ];
    }

    protected function message(): string
    {
        return 'Error en los datos de inicio de sesión';
    }
}
