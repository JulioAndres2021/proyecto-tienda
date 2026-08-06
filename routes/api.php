<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//RUTAS DEL GRUPO API VERSION 1
Route::prefix('v1')->group(function() {
    
    //RUTA DE PRODUCTOS
    Route::get('/productos', [ProductoController::class, 'index'])->name('api.productos');
});