<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categorias = Categoria::all();

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Categorías obtenidas correctamente.',
            'datos' => $categorias,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $validatedata = $request->validated(); //Validamos los datos traidos

        $categoria = Categoria::create($validatedata); //Los agregamos a la base

        return response()->json([
            'exito' => true,
            'codigo' => 201,
            'mensaje' => 'Categoría creada correctamente.',
            'datos' => $categoria,
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Categoría obtenida correctamente.',
            'datos' => $categoria,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria): JsonResponse
    {
        $validatedata = $request->validated(); //Validamos datos traidos

        $categoria->update($validatedata); //Actualizamos

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Categoría actualizada correctamente.',
            'datos' => $categoria,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria): JsonResponse
    {
        $categoria->delete(); //Borramos

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Categoría eliminada correctamente.',
        ], 200);
    }
}
