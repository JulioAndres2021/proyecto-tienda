<?php

namespace App\Http\Controllers\Api\V1;

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
    public function __construct(private CarritoService $carritoService) {
    }

    public function mostrar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'No se encontró el carrito.',
            ], 404);
        }

        $carrito->load('items.producto');

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Carrito obtenido correctamente.',
            'token_carrito' => $carrito->token,
            'datos' => $carrito,
        ]);
    }

    public function agregar(AgregarProductoCarritoRequest $request): JsonResponse
    {
        $datos = $request->validated();//Validamos

        $producto = Producto::findOrFail($datos['producto_id']);//Buscamos el producto

        /*Si viene un token válido → usar ese carrito
        Si no viene token → crear un carrito nuevo*/
        $carrito = $this->carritoService->obtener($request,true);

        //verificamos si el producto ya está en el carrito
        $item = ItemCarrito::where('carrito_id', $carrito->id)->where('producto_id', $producto->id)->first();

        //calculamos
        $cantidadActual = $item?->cantidad ?? 0;

        $cantidadFinal = $cantidadActual + $datos['cantidad'];

        //validamos stock
        if ($cantidadFinal > $producto->stock) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'Stock insuficiente.',
                'errores' => [
                    'stock' => [
                        "Stock disponible: {$producto->stock}."
                    ]
                ],
            ], 422);
        }

        if ($item) {
            $item->update(['cantidad' => $cantidadFinal,]);
        } else {
            $item = ItemCarrito::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $producto->id,
                'cantidad' => $datos['cantidad'],
                'precio_unitario' => $producto->precio,
            ]);
        }

        $item->load('producto');

        return response()->json([
            'exito' => true,
            'codigo' => 201,
            'mensaje' => 'Producto agregado al carrito.',
            'token_carrito' => $carrito->token,
            'datos' => $item,
        ], 201);
    }

    public function actualizar(ActualizarCantidadCarritoRequest $request, Producto $producto): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'No se encontró el carrito.',
            ], 404);
        }

        $item = ItemCarrito::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto->id)
            ->first();

        if (!$item) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'El producto no se encuentra en el carrito.',
            ], 404);
        }

        $cantidad = $request->validated()['cantidad'];

        if ($cantidad > $producto->stock) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'Stock insuficiente.',
                'errores' => [
                    'stock' => [
                        "Stock disponible: {$producto->stock}."
                    ]
                ],
            ], 422);
        }

        $item->update(['cantidad' => $cantidad,]);

        $item->load('producto');//carga la relacion del producto

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Cantidad actualizada correctamente.',
            'datos' => $item,
        ]);
    }

    public function eliminar(Request $request, Producto $producto): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'No se encontró el carrito.',
            ], 404);
        }

        $item = ItemCarrito::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto->id)
            ->first();

        if (!$item) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'El producto no se encuentra en el carrito.',
            ], 404);
        }

        $item->delete();
        
        //retornamos
        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Producto eliminado del carrito.',
        ]);
    }

    public function vaciar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'No se encontró el carrito.',
            ], 404);
        }

        $carrito->items()->delete();

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Carrito vaciado correctamente.',
        ]);
    }
}