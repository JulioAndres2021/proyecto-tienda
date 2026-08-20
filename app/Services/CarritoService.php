<?php

namespace App\Services;

use App\Models\Carrito;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarritoService
{
    public function obtener(Request $request, bool $crear = false): ?Carrito
    {
        $token = $request->header('X-Carrito-Token');

        if ($token) {
            $carrito = Carrito::where('token', $token)
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
        ]);
    }

    /* ----LOGICA DEL CARRITO--------
    subtotal = suma de precio_unitario × cantidad

    impuestos = subtotal × 21%

    envío:
    - si subtotal es mayor a 0 y menor a 50000 → $5000
    - si subtotal es 50000 o más → gratis

    total = subtotal + impuestos + envío
    ----------EJEMPLO------------------
    Producto A:
    $10.000 × 2 = $20.000

    Producto B:
    $5.000 × 2 = $10.000

    Subtotal:       30.000
    Impuestos 21%:   6.300
    Envío:           5.000
    ----------------------
    Total:           41.300

    */
    public function resumen(Carrito $carrito): array
    {
        $carrito->load('items.producto');

        /*
        obtiene todos los productos del carrito.
        sum(...) recorre cada item.
        Para cada item calcula: cantidad × precio_unitario
        precio_unitario se convierte a número decimal con (float).
        round(..., 2) redondea el resultado a dos decimales
        La función anónima function ($item) define el cálculo que se aplica a cada producto.
        */
        $subtotal = round(
            $carrito->items->sum(function ($item) {
                return $item->cantidad * (float) $item->precio_unitario;
            }),
            2
        );

        //calcula el impuesto
        $impuestos = round($subtotal * 0.21, 2);

        /*
        calcula el costo de envio
        La condición verifica que:
        El subtotal sea mayor que 0.
        El subtotal sea menor que 50000.
        Si ambas condiciones se cumplen, el envío cuesta $5000.00.
        Si no se cumplen, el envío es gratis ($0.00).
        */
        $costoEnvio = $subtotal > 0 && $subtotal < 50000
            ? 5000.00
            : 0.00;

        //Calcula el total
        $total = round($subtotal + $impuestos + $costoEnvio, 2);

        return [
            'subtotal' => $subtotal,
            'impuestos' => $impuestos,
            'costo_envio' => $costoEnvio,
            'total' => $total,
        ];
    }
}
