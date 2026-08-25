<?php

namespace App\DTOs\Carrito;

class AgregarProductoCarritoData
{
    public function __construct(
        public readonly int $productoId,
        public readonly int $cantidad,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productoId: (int) $data['producto_id'],
            cantidad: (int) $data['cantidad'],
        );
    }
}