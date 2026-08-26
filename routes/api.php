<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CarritoController;
use App\Http\Controllers\Api\V1\CategoriaController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\ProductoController;
use App\Http\Controllers\Api\V1\ResumenCompraController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Rutas públicas
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {

        Route::post(
            'register',
            [AuthController::class, 'register']
        )->middleware('throttle:register');

        Route::post(
            'login',
            [AuthController::class, 'login']
        )->middleware('throttle:login');
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas protegidas por JWT
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:api')->group(function () {

        /*
        | Auth
        */
        Route::prefix('auth')->group(function () {

            Route::get(
                'me',
                [AuthController::class, 'me']
            );

            Route::post(
                'logout',
                [AuthController::class, 'logout']
            );

            Route::post(
                'refresh',
                [AuthController::class, 'refresh']
            );
        });

        /*
        | Categorías
        */
        Route::apiResource(
            'categorias',
            CategoriaController::class
        );

        /*
        | Productos
        */
        Route::apiResource(
            'productos',
            ProductoController::class
        );

        /*
        |--------------------------------------------------------------------------
        | Crear/agregar productos al carrito
        |--------------------------------------------------------------------------
        |
        | Esta ruta queda protegida por JWT, pero NO por carrito.propietario,
        | porque puede crear un carrito nuevo.
        |
        */

        Route::post(
            'carrito/productos',
            [CarritoController::class, 'agregar']
        );

        /*
        |--------------------------------------------------------------------------
        | Rutas que requieren un carrito existente y propio
        |--------------------------------------------------------------------------
        */

        Route::middleware('carrito.propietario')->group(function () {

            Route::get(
                'carrito',
                [CarritoController::class, 'mostrar']
            );

            Route::put(
                'carrito/productos/{producto}',
                [CarritoController::class, 'actualizar']
            );

            Route::delete(
                'carrito/productos/{producto}',
                [CarritoController::class, 'eliminar']
            );

            Route::delete(
                'carrito',
                [CarritoController::class, 'vaciar']
            );

            Route::get(
                'carrito/resumen',
                [ResumenCompraController::class, 'mostrar']
            );

            Route::get(
                'checkout/revisar',
                [CheckoutController::class, 'revisar']
            );

            Route::post(
                'checkout/datos',
                [CheckoutController::class, 'registrarDatos']
            );

            Route::post(
                'checkout/confirmar',
                [CheckoutController::class, 'confirmar']
            );
        });
    });
});