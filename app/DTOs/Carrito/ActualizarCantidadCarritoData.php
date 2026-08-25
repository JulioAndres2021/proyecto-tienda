<?php

namespace App\DTOs\Carrito;

class ActualizarCantidadCarritoData
{
    public function __construct(
        public readonly int $cantidad,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            cantidad: (int) $data['cantidad'],
        );
    }
}