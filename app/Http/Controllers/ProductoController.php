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

    //Metodo Store, crea un nuevo producto en la base de datos
    public function store(Request $request) {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $producto = Producto::create($validatedData);
        return response()->json($producto, 201);
    }
}
