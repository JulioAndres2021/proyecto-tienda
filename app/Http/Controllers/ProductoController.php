<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    //Metodo Index, devuelve todos los productos en formato JSON
    public function index() {
        $productos = Producto::all();
        return response()->json($productos);
    }
}