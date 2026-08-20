<?php

namespace App\DTOs;

class DatosCheckoutDTO
{
    public function __construct(
        public readonly string $nombreCliente,
        public readonly string $email,
        public readonly string $direccionEnvio,
        public readonly string $ciudad,
        public readonly string $codigoPostal,
        public readonly string $metodoPago,
    ) {}
}