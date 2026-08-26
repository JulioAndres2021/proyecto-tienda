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
use App\Services\ProductoService;


class ProductoController extends Controller
{

    public function __construct(private ProductoService $productoService) {
    }

    public function index(): JsonResponse
    {
        $productos = $this->productoService->listar();

        return ApiResponse::success(ProductoResource::collection($productos), 'Productos obtenidos correctamente.');
    }

    public function store(StoreProductoRequest $request): JsonResponse 
    {
        $data = CreateProductoData::fromArray($request->validated());

        $producto = $this->productoService->crear($data);

        return ApiResponse::success(new ProductoResource($producto), 'Producto creado correctamente.',
            201
        );
    }

    public function show(Producto $producto): JsonResponse 
    {
        $producto->load([
            'categoria',
            'usuario',
            'actualizadoPor',
        ]);

        return ApiResponse::success(
            new ProductoResource($producto),
            'Producto obtenido correctamente.'
        );
    }

    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse 
    {
        $data = UpdateProductoData::fromArray($request->validated());

        $producto = $this->productoService->actualizar(
                $producto,
                $data
            );

        return ApiResponse::success(new ProductoResource($producto), 'Producto actualizado correctamente.'
        );
    }

    public function destroy(Producto $producto): JsonResponse 
    {
        $this->productoService->eliminar($producto);

        return ApiResponse::success(null, 'Producto eliminado correctamente.');
    }
}
