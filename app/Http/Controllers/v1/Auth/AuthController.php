<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Resources\v1\Auth\AuthUserResource;
use App\Response\ApiResponse;
use App\Services\v1\Auth\AuthServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Autenticación compartida: la usan intranet, landing y mobile.
 */
class AuthController extends Controller
{
    public function __construct(
        private AuthServices $authServices,
    ) {}

    /**
     * POST /api/v1/auth/login
     * Body: { dni, device_name }
     *
     * Si el DNI no está en la BD se consulta el proveedor de DNI y,
     * si existe, se registra automáticamente.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authServices->login($request->validated());

        return ApiResponse::success(
            $this->tokenPayload($result['user'], $result['token']) + ['is_new' => $result['is_new']],
            $result['is_new'] ? 'Bienvenido, tu cuenta fue creada' : 'Usuario autenticado',
        );
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new AuthUserResource($request->user()),
            'Usuario autenticado',
        );
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authServices->logout($request->user());

        return ApiResponse::success(null, 'Sesión cerrada correctamente', includeData: false);
    }

    /**
     * POST /api/v1/auth/logout-all
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authServices->logoutAll($request->user());

        return ApiResponse::success(null, 'Sesión cerrada en todos los dispositivos', includeData: false);
    }

    private function tokenPayload($user, string $token): array
    {
        return [
            'user' => new AuthUserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
