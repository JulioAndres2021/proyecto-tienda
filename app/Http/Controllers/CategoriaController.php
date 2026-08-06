<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    //Listamos todas las categorías en formato JSON
    public function index() {
        $categorias = Categoria::all();
        return response()->json($categorias); //retornamos las categorías en formato JSON
    }

    //Creamos una nueva categoría en la base de datos
    public function store(Request $request) {
        //Validamos los datos recibidos en la solicitud
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',]);
        
        //Creamos la categoría en la base de datos
        $categoria = Categoria::create($validatedData);
        return response()->json($categoria, 201); //retornamos la categoría creada en formato JSON con código de estado 201 (creado)
    }
}