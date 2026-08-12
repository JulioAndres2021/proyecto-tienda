<?php

use App\Http\Controllers\Api\V1\ProductoController;
use App\Http\Controllers\Api\V1\CategoriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//RUTAS DEL GRUPO API VERSION 1 (Le agregamos el prefix v1 a todas las rutas)
Route::prefix('v1')->group(function() {

     // PRODUCTOS
    Route::apiResource('productos', ProductoController::class)
        ->missing(function (Request $request) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'Producto no encontrado.',
            ], 404);
        });

    // CATEGORIAS
    Route::apiResource('categorias', CategoriaController::class)
        ->missing(function (Request $request) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'Categoría no encontrada.',
            ], 404);
        });


});
