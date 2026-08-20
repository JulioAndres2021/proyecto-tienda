<?php

namespace App\DTOs;

use App\Models\Compra;

class CompraConfirmadaDTO
{
    public function __construct(
        public readonly int $compraId,
        public readonly string $estado,
        public readonly float $subtotal,
        public readonly float $impuestos,
        public readonly float $costoEnvio,
        public readonly float $total,
    ) {}

    public static function desdeCompra(Compra $compra): self
    {
        return new self(
            compraId: $compra->id,
            estado: $compra->estado,
            subtotal: (float) $compra->subtotal,
            impuestos: (float) $compra->impuestos,
            costoEnvio: (float) $compra->costo_envio,
            total: (float) $compra->total,
        );
    }

    public function toArray(): array
    {
        return [
            'compra_id' => $this->compraId,
            'estado' => $this->estado,
            'subtotal' => $this->subtotal,
            'impuestos' => $this->impuestos,
            'costo_envio' => $this->costoEnvio,
            'total' => $this->total,
        ];
    }
}