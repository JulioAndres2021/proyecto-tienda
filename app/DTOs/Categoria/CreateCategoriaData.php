<?php

namespace App\DTOs\Categoria;

class CreateCategoriaData
{
    public function __construct(
        public readonly string $nombre,
        public readonly ?string $descripcion,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: $data['nombre'],
            descripcion: $data['descripcion'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ];
    }
}
