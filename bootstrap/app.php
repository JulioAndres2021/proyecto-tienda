<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
        
        // Renderizar respuestas JSON personalizadas
        $exceptions->render(function (Throwable $e, Request $request) {
            
            // Validar que la petición sea de API o espere JSON
            if ($request->is('api/*') || $request->expectsJson()) {
                
                // Controlar error 404
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'codigo'  => 404,
                        'mensaje' => 'Recurso no encontrado.'
                    ], 404);
                }

                // Controlar error 500 (y cualquier otra excepción no controlada)
                // En producción oculta el mensaje real por seguridad
                $isProduction = app()->environment('production');
                
                return response()->json([
                    'codigo'  => 500,
                    'mensaje' => 'Error interno del servidor.',
                    'error'   => "[]"
                ], 500);
            }
        });
        
    })->create();