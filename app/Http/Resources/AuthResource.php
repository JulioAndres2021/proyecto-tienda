<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'usuario' => new UserResource(
                $this['usuario']
            ),

            'autorizacion' => [
                'token' => $this['token'],
                'tipo' => $this['token_type'],
                'expira_en' => $this['expires_in'],
            ],
        ];
    }
}
