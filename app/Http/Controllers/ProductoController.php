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

    //Muestra un producto específico en formato JSON
    public function show($id) {
        $producto = Producto::find($id);
        //Si el producto existe, devuelve el producto en formato JSON, de lo contrario devuelve un mensaje de error
        if ($producto) {
            return response()->json($producto);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    //Actualiza un Producto especifico en la base de datos
    public function update(Request $request, $id) {
        $producto = Producto::find($id);
        //Si el producto existe, actualiza el producto en la base de datos, de lo contrario devuelve un mensaje de error
        if ($producto) {
            //Validamos los datos recibidos en la solicitud
            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'sometimes|required|numeric',
                'categoria_id' => 'sometimes|required|exists:categorias,id',
            ]);

            $producto->update($validatedData);
            return response()->json($producto);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    //Eliminamos un producto específico de la base de datos
    public function delete(Request $request, $id) {
        $producto = Producto::find($id);
        //Si el producto existe, lo eliminamos de la base de datos, de lo contrario devuelve un mensaje de error
        if ($producto) {
            $producto->delete();
            return response()->json(['message' => 'Producto eliminado correctamente']);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }
}
