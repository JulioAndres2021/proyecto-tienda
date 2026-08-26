<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'estado' => $this->estado,

            'cliente' => [
                'nombre' => $this->nombre_cliente,
                'email' => $this->email,
            ],

            'envio' => [
                'direccion' => $this->direccion_envio,
                'ciudad' => $this->ciudad,
                'codigo_postal' => $this->codigo_postal,
            ],

            'metodo_pago' => $this->metodo_pago,

            'resumen' => [
                'subtotal' => (float) $this->subtotal,
                'impuestos' => (float) $this->impuestos,
                'costo_envio' => (float) $this->costo_envio,
                'total' => (float) $this->total,
            ],

            'detalles' => DetalleCompraResource::collection(
                $this->whenLoaded('detalles')
            ),

            'created_at' => $this->created_at,
        ];
    }
}