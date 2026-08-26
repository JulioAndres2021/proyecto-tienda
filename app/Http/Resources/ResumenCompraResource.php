<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumenCompraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'subtotal' => (float) $this['subtotal'],
            'impuestos' => (float) $this['impuestos'],
            'costo_envio' => (float) $this['costo_envio'],
            'total' => (float) $this['total'],
        ];
    }
}