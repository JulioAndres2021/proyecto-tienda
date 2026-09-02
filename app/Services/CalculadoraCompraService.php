<?php

namespace App\Services;

class CalculadoraCompraService
{
    public function calcular(float $subtotal): array
    {
        $impuestos = round($subtotal * 0.21, 2);

        $costoEnvio = $subtotal > 0 && $subtotal < 50000
            ? 5000
            : 0;

        $total = round(
            $subtotal + $impuestos + $costoEnvio,
            2
        );

        return [
            'subtotal' => $subtotal,
            'impuestos' => $impuestos,
            'costo_envio' => $costoEnvio,
            'total' => $total,
        ];
    }
}
