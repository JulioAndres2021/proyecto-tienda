<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Producto\CreateProductoData;
use App\DTOs\Producto\UpdateProductoData;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;



class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $productos = Producto::with('categoria')->get();

        return ApiResponse::success(ProductoResource::collection($productos), 'Productos obtenidos correctamente.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request): JsonResponse
    {

        $data = CreateProductoData::fromArray($request->validated());

        $producto = Producto::create($data->toArray());

        $producto->load('categoria');

        return ApiResponse::success(new ProductoResource($producto), 'Producto creado correctamente.', 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto): JsonResponse
    {
        $producto->load('categoria');

        return ApiResponse::success(new ProductoResource($producto), 'Producto obtenido correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $data = UpdateProductoData::fromArray($request->validated());

        $producto->update($data->toArray());

        $producto->load('categoria');

        return ApiResponse::success(new ProductoResource($producto), 'Producto actualizado correctamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete();

        return ApiResponse::success(null, 'Producto eliminado correctamente.');

    }
}
