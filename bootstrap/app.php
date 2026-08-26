<?php


use App\Http\Responses\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use app\Http\Middleware\VerificarPropietarioCarrito;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'carrito.propietario' => \App\Http\Middleware\VerificarPropietarioCarrito::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') || $request->expectsJson()
        );

        /*
        |--------------------------------------------------------------------------
        | Error de validación - 422
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Los datos enviados no son válidos.',
                    422,
                    $e->errors()
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Token Vencido
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (TokenExpiredException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'El token ha expirado.',
                    401
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Token inválido
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (TokenInvalidException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'El token no es válido.',
                    401
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Para otros errores de tokens
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (JWTException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Token JWT no proporcionado o incorrecto.',
                    401
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Para demasiados intentos en login
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Demasiados intentos. Intente nuevamente más tarde.',
                    429
                );
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Para una ruta protegida no reciba token
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'No autenticado.',
                    401
                );
            }
        });




        /*
        |--------------------------------------------------------------------------
        | Modelo no encontrado - 404
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Recurso no encontrado.',
                    404
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Ruta no encontrada - 404
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Ruta no encontrada.',
                    404
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Método HTTP no permitido - 405
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Método HTTP no permitido para esta ruta.',
                    405
                );
            }
        });

})->create();