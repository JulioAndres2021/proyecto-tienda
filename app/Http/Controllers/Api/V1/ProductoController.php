<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
       $productos = Producto::with('categoria')->get();

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Productos obtenidos correctamente.',
            'datos' => $productos,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request): JsonResponse
    {
        $validatedata = $request->validated(); //Validamos los datos traidos

        $producto = Producto::create($validatedata); //Los agregamos a la base

        return response()->json([
            'exito' => true,
            'codigo' => 201,
            'mensaje' => 'Producto creado correctamente.',
            'datos' => $producto,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Producto obtenido correctamente.',
            'datos' => $producto,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $validatedata = $request->validated(); //Validamos datos traidos

        $producto->update($validatedata); //Actualizamos

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Producto actualizado correctamente.',
            'datos' => $producto,
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete(); //Borramos

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Producto eliminado correctamente.',
        ], 200);


    }
}
