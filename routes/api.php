<?php

use App\Http\Controllers\Api\V1\CarritoController;
use App\Http\Controllers\Api\V1\CategoriaController;
use App\Http\Controllers\Api\V1\ProductoController;
use App\Http\Controllers\Api\V1\ResumenCompraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//RUTAS DEL GRUPO API VERSION 1 (Le agregamos el prefix v1 a todas las rutas)
Route::prefix('v1')->group(function() {

     // PRODUCTOS
    Route::apiResource('productos', ProductoController::class)->middleware('throttle:10,1');;


    // CATEGORIAS
    Route::apiResource('categorias', CategoriaController::class)->middleware('throttle:10,1');

    //RUTAS PARA CARRITO
    Route::get('carrito', [CarritoController::class, 'mostrar']);

    Route::post('carrito/productos', [CarritoController::class, 'agregar']);

    Route::put('carrito/productos/{producto}', [CarritoController::class, 'actualizar']);

    Route::delete('carrito/productos/{producto}', [CarritoController::class, 'eliminar']);

    Route::delete('carrito', [CarritoController::class, 'vaciar']);

    Route::get('carrito/resumen', [ResumenCompraController::class, 'mostrar']);

});
