<?php

namespace App\Services;

use App\Models\ItemCarrito;
use App\Models\Producto;
use Illuminate\Validation\ValidationException;

use App\Models\Carrito;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarritoService
{
    public function __construct(
        private CalculadoraCompraService $calculadoraCompraService
    ) {
    }
    public function obtener(Request $request, bool $crear = false): ?Carrito
    {
        $token = $request->header('X-Carrito-Token');

        $usuarioId = auth('api')->id();

        if ($token) {
            $carrito = Carrito::where('token', $token)
                ->where('usuario_id', $usuarioId)
                ->where('estado', 'activo')
                ->first();

            if ($carrito) {
                return $carrito;
            }
        }

        if (!$crear) {
            return null;
        }

        return Carrito::create([
            'token' => (string) Str::uuid(),
            'estado' => 'activo',
            'usuario_id' => $usuarioId,
        ]);
    }

    public function resumen(Carrito $carrito): array
    {
        $carrito->loadMissing('items');

        $subtotal = $carrito->items->sum(function ($item) {
            return $item->cantidad * (float) $item->precio_unitario;
        });

        return $this->calculadoraCompraService->calcular(
            (float) $subtotal
        );
    }

    public function agregarProducto(Carrito $carrito, Producto $producto, int $cantidad): ItemCarrito
    {
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

        $cantidadFinal = $cantidadActual + $cantidad;

        $this->validarStock(
            $producto,
            $cantidadFinal
        );

        if ($item) {
            $item->update([
                'cantidad' => $cantidadFinal,
            ]);
        } else {
            $item = ItemCarrito::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio,
            ]);
        }

        return $item->load('producto');
    }

    public function actualizarCantidad(Carrito $carrito, Producto $producto, int $cantidad): ItemCarrito
    {
        $item = ItemCarrito::where(
            'carrito_id',
            $carrito->id
        )
            ->where(
                'producto_id',
                $producto->id
            )
            ->first();

        if (!$item) {
            throw ValidationException::withMessages([
                'producto' => [
                    'El producto no se encuentra en el carrito.',
                ],
            ]);
        }

        $this->validarStock(
            $producto,
            $cantidad
        );

        $item->update([
            'cantidad' => $cantidad,
        ]);

        return $item->load('producto');
    }

    private function validarStock(Producto $producto, int $cantidad): void
    {
        if ($cantidad > $producto->stock) {
            throw ValidationException::withMessages([
                'stock' => [
                    "Stock disponible: {$producto->stock}.",
                ],
            ]);
        }
    }

    public function eliminarProducto(Carrito $carrito, Producto $producto): void
    {
        $item = ItemCarrito::where(
            'carrito_id',
            $carrito->id
        )
            ->where(
                'producto_id',
                $producto->id
            )
            ->first();

        if (!$item) {
            throw ValidationException::withMessages([
                'producto' => [
                    'El producto no se encuentra en el carrito.',
                ],
            ]);
        }

        $item->delete();
    }

    public function vaciar(Carrito $carrito): void
    {
        $carrito->items()->delete();
    }


}
