<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

            if (!($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'exito' => false,
                    'codigo' => 422,
                    'mensaje' => 'Los datos enviados no son válidos.',
                    'errores' => $e->errors(),
                ], 422);
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json([
                    'exito' => false,
                    'codigo' => 404,
                    'mensaje' => 'Recurso no encontrado.',
                ], 404);
            }

            if ($e instanceof HttpExceptionInterface) {$codigo = $e->getStatusCode();

                return response()->json([
                    'exito' => false,
                    'codigo' => $codigo,
                    'mensaje' => $codigo === 405 ? 'Método HTTP no permitido para esta ruta.' : 'Error en la petición.',
                ], $codigo);
            }

            return response()->json([
                'exito' => false,
                'codigo' => 500,
                'mensaje' => 'Error interno del servidor.',
            ], 500);
        });

    })->create();
