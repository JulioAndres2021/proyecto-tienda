<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Carrito\AgregarProductoCarritoData;
use App\DTOs\Carrito\ActualizarCantidadCarritoData;
use App\Http\Resources\CarritoResource;
use App\Http\Resources\ItemCarritoResource;
use App\Http\Responses\ApiResponse;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgregarProductoCarritoRequest;
use App\Http\Requests\ActualizarCantidadCarritoRequest;
use App\Models\ItemCarrito;
use App\Models\Producto;
use App\Services\CarritoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    //Usamos el constructor para obtener el token
    public function __construct(private CarritoService $carritoService) {
    }

    /*
    | mostrar
    |-muestra un carrito con sus productos-
    */
    public function mostrar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }

        $carrito->load('items.producto');

        return ApiResponse::success(new CarritoResource($carrito), 'Carrito obtenido correctamente.');
    }
    /*
    | agregar
    |-agrega un producto al carrito-
    */
    public function agregar(AgregarProductoCarritoRequest $request): JsonResponse 
    {
        $data = AgregarProductoCarritoData::fromArray(
            $request->validated()
        );

        $producto = Producto::findOrFail(
            $data->productoId
        );

        $carrito = $this->carritoService->obtener($request, true);

        $item = $this->carritoService->agregarProducto(
            $carrito,
            $producto,
            $data->cantidad
        );

        return ApiResponse::success(
            [
                'token_carrito' => $carrito->token,
                'item' => new ItemCarritoResource($item),
            ],
            'Producto agregado al carrito.',
            201
        );
    }

    /*
    | actualizar
    |-actualiza un producto en el carrito-
    */
    public function actualizar(ActualizarCantidadCarritoRequest $request, Producto $producto): JsonResponse 
    {
        $data = ActualizarCantidadCarritoData::fromArray(
            $request->validated()
        );

        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }

        $item = $this->carritoService->actualizarCantidad(
            $carrito,
            $producto,
            $data->cantidad
        );

        return ApiResponse::success(
            new ItemCarritoResource($item),
            'Cantidad actualizada correctamente.'
        );
    }
    
    /*
    | eliminar
    |-elimina un producto en el carrito-
    */
    public function eliminar(Request $request, Producto $producto): JsonResponse 
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }

        $this->carritoService->eliminarProducto(
            $carrito,
            $producto
        );

        return ApiResponse::success(
            null,
            'Producto eliminado del carrito.'
        );
    }

    /*
    | vaciar
    |-vacia el carrito-
    */
    public function vaciar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }

        $this->carritoService->vaciar($carrito);

        return ApiResponse::success(
            null,
            'Carrito vaciado correctamente.'
        );
    }
}