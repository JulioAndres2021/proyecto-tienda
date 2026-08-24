<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Categoria\CreateCategoriaData;
use App\DTOs\Categoria\UpdateCategoriaData;
use App\Http\Resources\CategoriaResource;
use App\Http\Responses\ApiResponse;
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

        return ApiResponse::success(CategoriaResource::collection($categorias), 'Categorías obtenidas correctamente.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoriaRequest $request): JsonResponse 
    {
        $data = CreateCategoriaData::fromArray($request->validated());

        $categoria = Categoria::create($data->toArray());

        return ApiResponse::success(new CategoriaResource($categoria), 'Categoría creada correctamente.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria): JsonResponse 
    {
        return ApiResponse::success(new CategoriaResource($categoria), 'Categoría obtenida correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria): JsonResponse 
    {
        $data = UpdateCategoriaData::fromArray($request->validated());

        $categoria->update($data->toArray());

        return ApiResponse::success(new CategoriaResource($categoria), 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria): JsonResponse 
    {
        $categoria->delete();

        return ApiResponse::success(null, 'Categoría eliminada correctamente.');
    }
}