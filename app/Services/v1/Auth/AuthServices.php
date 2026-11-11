<?php

namespace App\Services\v1\Auth;

use App\Exceptions\ApiException;
use App\Models\User;
use App\Repositories\v1\Auth\AuthRepositoryInterface;
use App\Services\v1\Dni\DniProviderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autenticación compartida por intranet, landing y mobile.
 */
class AuthServices
{
    public function __construct(
        private AuthRepositoryInterface $authRepository,
        private DniProviderInterface $dniProvider,
    ) {}

    /**
     * Login solo con DNI:
     *  1. Si el DNI ya está en la BD → entra directo (no se consulta nada externo).
     *  2. Si no está → se consulta el proveedor de DNI (hoy padrón ficticio,
     *     luego API pública). Si lo encuentra, se registra automáticamente.
     *  3. Si tampoco existe en el proveedor → no entra.
     *
     * @return array{user: User, token: string, is_new: bool}
     *
     * @throws ApiException
     */
    public function login(array $data): array
    {
        $user = $this->authRepository->findUserByDni($data['dni']);
        $isNew = false;

        if (! $user) {
            $persona = $this->dniProvider->buscar($data['dni']);

            if (! $persona) {
                throw new ApiException('El DNI ingresado no está registrado', Response::HTTP_UNAUTHORIZED);
            }

            $user = $this->authRepository->createUser([
                'dni' => $persona['dni'],
                'name' => $persona['nombre_completo'],
            ]);
            $isNew = true;
        }

        $token = $this->authRepository->createToken($user, $data['device_name']);

        return ['user' => $user, 'token' => $token, 'is_new' => $isNew];
    }

    public function logout(User $user): void
    {
        $this->authRepository->revokeCurrentToken($user);
    }

    public function logoutAll(User $user): void
    {
        $this->authRepository->revokeAllTokens($user);
    }
}
