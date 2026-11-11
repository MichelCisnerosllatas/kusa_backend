<?php

namespace App\Exceptions;

use Exception;

/**
 * Error de negocio controlado. Los Services la lanzan y el handler global
 * (bootstrap/app.php) la convierte en ApiResponse::error().
 *
 * Ejemplo: throw new ApiException('Credenciales incorrectas', 401);
 */
class ApiException extends Exception
{
    public function __construct(
        string $message = 'Error',
        private int $status = 400,
        private mixed $errors = null,
    ) {
        parent::__construct($message);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getErrors(): mixed
    {
        return $this->errors;
    }
}
