<?php

namespace App\Services;

use App\Contracts\PagoServiceInterface;

class PagoService implements PagoServiceInterface
{
    public function aprobar(float $monto,string $metodoPago): bool {
        /*
         * Simulación.
         * En un proyecto real acá podría llamarse
         * a Mercado Pago, Stripe, etc.
         */
        return true;
    }
}
