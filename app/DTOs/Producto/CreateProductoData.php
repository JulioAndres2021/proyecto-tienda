<?php

namespace App\DTOs\Producto;

class CreateProductoData
{
    public function __construct(
        public readonly string $sku,
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly float $precio,
        public readonly int $stock,
        public readonly int $categoriaId,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            sku: $data['sku'],
            nombre: $data['nombre'],
            descripcion: $data['descripcion'] ?? null,
            precio: (float) $data['precio'],
            stock: (int) $data['stock'],
            categoriaId: (int) $data['categoria_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'sku' => $this->sku,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'categoria_id' => $this->categoriaId,
        ];
    }
}
