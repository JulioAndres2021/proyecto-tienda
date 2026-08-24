<?php

namespace App\DTOs\Categoria;

class UpdateCategoriaData
{
    public function __construct(
        public readonly ?string $nombre,
        public readonly ?string $descripcion,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: $data['nombre'] ?? null,
            descripcion: $data['descripcion'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ], fn ($value) => $value !== null);
    }
}
