<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Contracts\PagoServiceInterface;
use App\Services\PagoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PagoServiceInterface::class,PagoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //COMPARTIMOS LA VARIABLE DE ENTORNO DE LA VERSION DEL SISTEMA CON TODAS LAS VISTAS
        View::share('appRelease', config('app.release'));

        //PARA QUE NO DEVUELVA EL DATA EN LAS RESPUESTAS JSON
        JsonResource::withoutWrapping();

        //ESTABLECEMOS UN LIMITE PARA EL LOGIN
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(strtolower($request->input('email')).'|'.$request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip());
        });


    }
}
