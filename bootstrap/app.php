<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\ForceJsonResponse;
use App\Response\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Todas las peticiones a /api se tratan como JSON.
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        // API pura: nunca redirigir a una ruta "login" web, responder 401 JSON.
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Errores de negocio lanzados desde los Services.
        $exceptions->render(fn (ApiException $e) => ApiResponse::error(
            $e->getMessage(),
            $e->getStatus(),
            $e->getErrors(),
        ));

        // 422 - Validación (para las que no pasan por ApiFormRequest).
        $exceptions->render(fn (ValidationException $e) => ApiResponse::error(
            'Errores de validación',
            422,
            $e->errors(),
        ));

        // 401 - Sin token o token inválido.
        $exceptions->render(fn (AuthenticationException $e) => ApiResponse::error('No autenticado', 401));

        // 403 - Sin permisos.
        $exceptions->render(fn (AuthorizationException|AccessDeniedHttpException $e) => ApiResponse::error(
            'No tienes permisos para realizar esta acción',
            403,
        ));

        // 404 - Ruta o registro no encontrado.
        $exceptions->render(fn (NotFoundHttpException $e) => ApiResponse::error('Recurso no encontrado', 404));

        // 405 - Método HTTP no permitido.
        $exceptions->render(fn (MethodNotAllowedHttpException $e) => ApiResponse::error('Método no permitido', 405));

        // 429 - Demasiadas peticiones (throttle).
        $exceptions->render(fn (ThrottleRequestsException $e) => ApiResponse::error(
            'Demasiadas peticiones, intenta nuevamente en un momento',
            429,
        ));
    })->create();
