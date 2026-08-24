<?php

namespace App\DTOs\Producto;

class UpdateProductoData
{
    public function __construct(
        public readonly ?string $sku,
        public readonly ?string $nombre,
        public readonly ?string $descripcion,
        public readonly ?float $precio,
        public readonly ?int $stock,
        public readonly ?int $categoriaId,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            sku: $data['sku'] ?? null,
            nombre: $data['nombre'] ?? null,
            descripcion: $data['descripcion'] ?? null,
            precio: isset($data['precio'])
                ? (float) $data['precio']
                : null,
            stock: isset($data['stock'])
                ? (int) $data['stock']
                : null,
            categoriaId: isset($data['categoria_id'])
                ? (int) $data['categoria_id']
                : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'sku' => $this->sku,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'categoria_id' => $this->categoriaId,
        ], fn ($value) => $value !== null);
    }
}
