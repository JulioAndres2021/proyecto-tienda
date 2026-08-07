<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//RUTAS DEL GRUPO API VERSION 1 (Le agregamos el prefix v1 a todas las rutas)
Route::prefix('v1')->group(function() {

    //PRODUCTOS
    //Obtener todos los productos en formato JSON
    Route::get('/productos', [ProductoController::class, 'index'])->name('api.productos');
    //Crear un nuevo producto en la base de datos
    Route::post('/productos', [ProductoController::class, 'store'])->name('api.productos.store');
    //Muestra un producto específico en formato JSON
    Route::get('/productos/{id}', [ProductoController::class, 'show'])->name('api.productos.show');
    //Actualiza un producto específico en la base de datos
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('api.productos.update');
    //Elimina un producto específico de la base de datos
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('api.productos.destroy');
    //CATEGORIAS
    //Obtener todas las categorías en formato JSON
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('api.categorias');
    //Crear una nueva categoría en la base de datos
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('api.categorias.store');

});
