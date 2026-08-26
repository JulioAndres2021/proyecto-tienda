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
    | Rutas públicas de autenticación
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {

        Route::post('register', [AuthController::class, 'register']);

        Route::post('login', [AuthController::class, 'login']);
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

            Route::get('me', [AuthController::class, 'me']);

            Route::post('logout', [AuthController::class, 'logout']);

            Route::post('refresh', [AuthController::class, 'refresh']);
        });

        /*
        | Categorías
        */
        Route::apiResource('categorias', CategoriaController::class);

        /*
        | Productos
        */
        Route::apiResource('productos', ProductoController::class);

        /*
        | Carrito
        */
        Route::get('carrito', [CarritoController::class, 'mostrar']);

        Route::post('carrito/productos', [CarritoController::class, 'agregar']);

        Route::put('carrito/productos/{producto}', [CarritoController::class, 'actualizar']);

        Route::delete('carrito/productos/{producto}', [CarritoController::class, 'eliminar']);

        Route::delete('carrito', [CarritoController::class, 'vaciar']);

        /*
        | Resumen
        */
        Route::get('carrito/resumen', [ResumenCompraController::class, 'mostrar']);

        /*
        | Checkout
        */
        Route::get('checkout/revisar', [CheckoutController::class, 'revisar']);

        Route::post('checkout/datos', [CheckoutController::class, 'registrarDatos']);

        Route::post('checkout/confirmar', [CheckoutController::class, 'confirmar']);
    });
});
