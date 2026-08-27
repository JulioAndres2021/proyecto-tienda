<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemCarritoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto_id' => $this->producto_id,
            'cantidad' => $this->cantidad,
            'precio_unitario' => (float) $this->precio_unitario,

            'subtotal' => round(
                $this->cantidad * (float) $this->precio_unitario,
                2
            ),

            'producto' => new ProductoResource($this->whenLoaded('producto')),
        ];
    }
}