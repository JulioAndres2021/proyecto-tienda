<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /*
    Clase para manejar los errores en cada respuesta API
    en este caso success y error.
    Con esto evitás repetir en todos los controladores:
    return response()->json([
        'exito' => true,
        'codigo' => 200,
        'mensaje' => '...',
        'datos' => ...
    ]);
    Y pasás a usar:
    return ApiResponse::success(
        $datos,
        'Producto obtenido correctamente.'
    );
    */
    
    public static function success(
        mixed $data = null,
        string $mensaje = 'Operación realizada correctamente.',
        int $codigo = 200
    ): JsonResponse {
        return response()->json([
            'exito' => true,
            'codigo' => $codigo,
            'mensaje' => $mensaje,
            'datos' => $data,
        ], $codigo);
    }

    public static function error(
        string $mensaje,
        int $codigo,
        mixed $errores = null
    ): JsonResponse {
        $respuesta = [
            'exito' => false,
            'codigo' => $codigo,
            'mensaje' => $mensaje,
        ];

        if ($errores !== null) {
            $respuesta['errores'] = $errores;
        }

        return response()->json(
            $respuesta,
            $codigo
        );
    }
}