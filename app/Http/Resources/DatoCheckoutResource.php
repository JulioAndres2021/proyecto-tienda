<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatoCheckoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'carrito_id' => $this->carrito_id,
            'nombre_cliente' => $this->nombre_cliente,
            'email' => $this->email,
            'direccion_envio' => $this->direccion_envio,
            'ciudad' => $this->ciudad,
            'codigo_postal' => $this->codigo_postal,
            'metodo_pago' => $this->metodo_pago,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}