<?php

namespace App\Contracts;

interface PagoServiceInterface
{
    public function aprobar(float $monto, string $metodoPago): bool;
}
