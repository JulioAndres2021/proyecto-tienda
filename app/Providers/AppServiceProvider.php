<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
    }
}