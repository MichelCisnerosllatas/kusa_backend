<?php

namespace App\Providers;

use App\Repositories\v1\Auth\AuthRepository;
use App\Repositories\v1\Auth\AuthRepositoryInterface;
use App\Repositories\v1\Mobile\Calificar\CalificarRepository;
use App\Repositories\v1\Mobile\Calificar\CalificarRepositoryInterface;
use App\Services\v1\Dni\DniProviderInterface;
use App\Services\v1\Dni\PadronFicticioDniProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ---------------------------------------------------------------
        // Repositorios: Interface => Implementación
        // ---------------------------------------------------------------

        // AUTH (compartido)
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);

        // Proveedor de DNI: hoy padrón ficticio. Para usar la API pública
        // solo cambia la implementación aquí.
        $this->app->bind(DniProviderInterface::class, PadronFicticioDniProvider::class);

        // INTRANET

        // LANDING

        // MOBILE
        $this->app->bind(CalificarRepositoryInterface::class, CalificarRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ---------------------------------------------------------------
        // Límites de peticiones (se usan como middleware "throttle:nombre")
        // ---------------------------------------------------------------

        // Login / registro: máximo 6 intentos por minuto por IP.
        // Al superarlo responde 429 (Demasiadas peticiones).
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });
    }
}
