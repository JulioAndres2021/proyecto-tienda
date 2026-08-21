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

    public static function desdeArray(array $datos): self
    {
        return new self(
            nombreCliente: $datos['nombre_cliente'],
            email: $datos['email'],
            direccionEnvio: $datos['direccion_envio'],
            ciudad: $datos['ciudad'],
            codigoPostal: $datos['codigo_postal'],
            metodoPago: $datos['metodo_pago'],
        );
    }

    public function toArray(): array
    {
        return [
            'nombre_cliente' => $this->nombreCliente,
            'email' => $this->email,
            'direccion_envio' => $this->direccionEnvio,
            'ciudad' => $this->ciudad,
            'codigo_postal' => $this->codigoPostal,
            'metodo_pago' => $this->metodoPago,
        ];
    }
}
