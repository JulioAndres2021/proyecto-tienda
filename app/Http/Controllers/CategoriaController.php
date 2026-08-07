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

    //Actualizamos una categoría específica en la base de datos
    public function update(Request $request, $id) {
        $categoria = Categoria::find($id);
        //Si la categoría existe, actualizamos la categoría en la base de datos, de lo contrario devolvemos un mensaje de error
        if ($categoria) {
            //Validamos los datos recibidos en la solicitud
            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string',
            ]);

            $categoria->update($validatedData);
            return response()->json($categoria); //retornamos la categoría actualizada en formato JSON
        } else {
            return response()->json(['message' => 'Categoría no encontrada'], 404); //retornamos un mensaje de error con código de estado 404 (no encontrado)
        }
    }

    //Buscamos una categoría específica en la base de datos
    public function show($id) {
        $categoria = Categoria::find($id);
        //Si la categoría existe, devolvemos la categoría en formato JSON, de lo contrario devolvemos un mensaje de error
        if ($categoria) {
            return response()->json($categoria); //retornamos la categoría en formato JSON
        } else {
            return response()->json(['message' => 'Categoría no encontrada'], 404); //retornamos un mensaje de error con código de estado 404 (no encontrado)
        }
    }

    //Eliminamos una categoría específica de la base de datos
    public function destroy($id) {
        $categoria = Categoria::find($id);
        //Si la categoría existe, eliminamos la categoría de la base de datos, de lo contrario devolvemos un mensaje de error
        if ($categoria) {
            $categoria->delete();
            return response()->json(['message' => 'Categoría eliminada']); //retornamos un mensaje de éxito
        } else {
            return response()->json(['message' => 'Categoría no encontrada'], 404); //retornamos un mensaje de error con código de estado 404 (no encontrado)
        }
    }
}
