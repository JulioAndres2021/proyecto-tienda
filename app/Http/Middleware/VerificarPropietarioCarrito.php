<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Models\Carrito;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPropietarioCarrito
{
    public function handle(Request $request, Closure $next): Response 
    {
        $token = $request->header('X-Carrito-Token');

        if (!$token) {
            return ApiResponse::error(
                'Debe enviar el token del carrito.',
                401
            );
        }

        $carrito = Carrito::where('token', $token)->first();

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }

        if ($carrito->usuario_id !== auth('api')->id()) 
        {
            return ApiResponse::error(
                'No tiene permisos para acceder a este carrito.',
                403
            );
        }

        return $next($request);
    }
}