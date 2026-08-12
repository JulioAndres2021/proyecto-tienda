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
        $productos = Producto::all(); //Tomamos todos los datos

        return response()->json($productos); //retornamos
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request): JsonResponse
    {
        $validatedata = $request->validated(); //Validamos los datos traidos

        $productos = Producto::create($validatedata); //Los agregamos a la base

        return response()->json($productos, 201); //retornamos
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        return response()->json($producto); //retornamos
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $validatedata = $request->validated(); //Validamos datos traidos

        $producto->update($validatedata); //Actualizamos

        return response()->json($producto); //Retornamos

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete(); //Borramos

        return response()->json('Producto eliminado', 204); //Mostramos mensaje


    }
}
