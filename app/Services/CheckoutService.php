<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Compra;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function confirmar(Carrito $carrito): Compra
    {
        $carrito->load([
            'items.producto',
            'datosCheckout',
        ]);

        $this->validarCarrito($carrito);

        $resumen = app(CarritoService::class)
            ->resumen($carrito);

        return DB::transaction(function () use (
            $carrito,
            $resumen
        ) {
            $datos = $carrito->datosCheckout;

            $compra = Compra::create([
                'carrito_id' => $carrito->id,
                'nombre_cliente' => $datos->nombre_cliente,
                'email' => $datos->email,
                'direccion_envio' => $datos->direccion_envio,
                'ciudad' => $datos->ciudad,
                'codigo_postal' => $datos->codigo_postal,
                'metodo_pago' => $datos->metodo_pago,

                'subtotal' => $resumen['subtotal'],
                'impuestos' => $resumen['impuestos'],
                'costo_envio' => $resumen['costo_envio'],
                'total' => $resumen['total'],

                'estado' => 'confirmada',
            ]);

            foreach ($carrito->items as $item) {
                $compra->detalles()->create([
                    'producto_id' => $item->producto->id,
                    'nombre_producto' => $item->producto->nombre,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'subtotal' => round(
                        $item->cantidad
                        * (float) $item->precio_unitario,
                        2
                    ),
                ]);

                $item->producto->decrement(
                    'stock',
                    $item->cantidad
                );
            }

            $carrito->update([
                'estado' => 'comprado',
            ]);

            return $compra->load('detalles');
        });
    }

    private function validarCarrito(Carrito $carrito): void
    {
        if ($carrito->items->isEmpty()) {
            throw ValidationException::withMessages([
                'carrito' => [
                    'El carrito está vacío.',
                ],
            ]);
        }

        if (!$carrito->datosCheckout) {
            throw ValidationException::withMessages([
                'checkout' => [
                    'Primero debe registrar los datos de checkout.',
                ],
            ]);
        }

        foreach ($carrito->items as $item) {
            if ($item->cantidad > $item->producto->stock) {
                throw ValidationException::withMessages([
                    'stock' => [
                        "Stock insuficiente para {$item->producto->nombre}.",
                    ],
                ]);
            }
        }
    }
}