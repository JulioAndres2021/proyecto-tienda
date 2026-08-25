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

        $carrito = $this->carritoService->obtener(
            $request,
            true
        );

        $item = ItemCarrito::where(
            'carrito_id',
            $carrito->id
        )
            ->where(
                'producto_id',
                $producto->id
            )
            ->first();

        $cantidadActual = $item?->cantidad ?? 0;

        $cantidadFinal =
            $cantidadActual + $data->cantidad;

        if ($cantidadFinal > $producto->stock) {
            return ApiResponse::error(
                'Stock insuficiente.',
                422,
                [
                    'stock' => [
                        "Stock disponible: {$producto->stock}."
                    ],
                ]
            );
        }

        if ($item) {
            $item->update([
                'cantidad' => $cantidadFinal,
            ]);
        } else {
            $item = ItemCarrito::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $producto->id,
                'cantidad' => $data->cantidad,
                'precio_unitario' => $producto->precio,
            ]);
        }

        $item->load('producto');

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
        $data = ActualizarCantidadCarritoData::fromArray($request->validated());

        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error('No se encontró el carrito.', 404);
        }

        $item = ItemCarrito::where('carrito_id', $carrito->id)
            ->where(
                'producto_id',
                $producto->id
            )
            ->first();

        if (!$item) {return ApiResponse::error('El producto no se encuentra en el carrito.', 404);
        }

        if ($data->cantidad > $producto->stock) {
            return ApiResponse::error(
                'Stock insuficiente.',
                422,
                [
                    'stock' => [
                        "Stock disponible: {$producto->stock}."
                    ],
                ]
            );
        }

        $item->update([
            'cantidad' => $data->cantidad,
        ]);

        $item->load('producto');

        return ApiResponse::success(new ItemCarritoResource($item), 'Cantidad actualizada correctamente.');
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

        $item = ItemCarrito::where('carrito_id', $carrito->id)
            ->where(
                'producto_id',
                $producto->id
            )
            ->first();

        if (!$item) {
            return ApiResponse::error(
                'El producto no se encuentra en el carrito.',
                404
            );
        }

        $item->delete();

        return ApiResponse::success(null, 'Producto eliminado del carrito.');
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

        $carrito->items()->delete();

        return ApiResponse::success(null, 'Carrito vaciado correctamente.');
    }
}