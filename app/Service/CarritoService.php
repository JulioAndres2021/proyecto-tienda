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
            return Carrito::where('token', $token)->where('estado', 'activo')->first();
        }

        if (!$crear) {
            return null;
        }

        return Carrito::create([
            'token' => (string) Str::uuid(),
            'estado' => 'activo',
        ]);
    }

    public function resumen(Carrito $carrito): array
    {
        $carrito->load('items.producto');
        $subtotal = round($carrito->items->sum(fn ($item) => $item->cantidad * (float) $item->precio_unitario), 2);
        $impuestos = round($subtotal * 0.21, 2);
        $costoEnvio = $subtotal > 0 && $subtotal < 50000 ? 5000.00 : 0.00;
        $total = round($subtotal + $impuestos + $costoEnvio, 2);

        return [
            'subtotal' => $subtotal,
            'impuestos' => $impuestos,
            'costo_envio' => $costoEnvio,
            'total' => $total,
        ];
    }
}