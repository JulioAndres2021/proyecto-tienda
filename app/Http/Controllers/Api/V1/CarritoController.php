<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarCantidadCarritoRequest;
use App\Http\Requests\AgregarProductoCarritoRequest;
use App\Models\ItemCarrito;
use App\Models\Producto;
use App\Services\CarritoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function __construct(private CarritoService $carritoService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return response()->json([
                'exito' => true,
                'codigo' => 200,
                'mensaje' => 'El carrito está vacío.',
                'datos' => ['items' => []],
            ]);
        }

        $carrito->load('items.producto');

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Carrito obtenido correctamente.',
            'token_carrito' => $carrito->token,
            'datos' => ['items' => $carrito->items],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgregarProductoCarritoRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $producto = Producto::findOrFail($datos['producto_id']);
        $carrito = $this->carritoService->obtener($request, true);//Obtiene el token

        $item = ItemCarrito::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto->id)
            ->first();

        $cantidadFinal = ($item?->cantidad ?? 0) + $datos['cantidad'];

        if ($cantidadFinal > $producto->stock) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'Stock insuficiente para agregar esa cantidad.',
                'errores' => ['stock' => ["Stock disponible: {$producto->stock}."]],
            ], 422);
        }

        if ($item) {
            $item->update(['cantidad' => $cantidadFinal, 'precio_unitario' => $producto->precio]);
        } else {
            $item = ItemCarrito::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $producto->id,
                'cantidad' => $datos['cantidad'],
                'precio_unitario' => $producto->precio,
            ]);
        }

        return response()->json([
            'exito' => true,
            'codigo' => 201,
            'mensaje' => 'Producto agregado al carrito.',
            'token_carrito' => $carrito->token,
            'datos' => $item->load('producto'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActualizarCantidadCarritoRequest $request, Producto $producto): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return $this->carritoNoEncontrado();
        }

        $cantidad = $request->validated()['cantidad'];

        if ($cantidad > $producto->stock) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'Stock insuficiente para actualizar esa cantidad.',
                'errores' => ['stock' => ["Stock disponible: {$producto->stock}."]],
            ], 422);
        }

        $item = ItemCarrito::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto->id)
            ->first();

        if (!$item) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'El producto no está en el carrito.',
            ], 404);
        }

        $item->update(['cantidad' => $cantidad, 'precio_unitario' => $producto->precio]);

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Cantidad actualizada correctamente.',
            'token_carrito' => $carrito->token,
            'datos' => $item->load('producto'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Producto $producto): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return $this->carritoNoEncontrado();
        }

        $eliminados = ItemCarrito::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto->id)
            ->delete();

        if (!$eliminados) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'El producto no está en el carrito.',
            ], 404);
        }

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Producto eliminado del carrito.',
        ]);
    }

    public function vaciar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if ($carrito) {
            $carrito->items()->delete();
        }

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Carrito vaciado correctamente.',
        ]);
    }

    private function carritoNoEncontrado(): JsonResponse
    {
        return response()->json([
            'exito' => false,
            'codigo' => 404,
            'mensaje' => 'Carrito no encontrado. Enviá un X-Carrito-Token válido.',
        ], 404);
    }

}