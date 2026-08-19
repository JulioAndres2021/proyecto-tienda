<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CarritoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResumenCompraController extends Controller
{
    public function __construct(private CarritoService $carritoService) {}

    public function mostrar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'Carrito no encontrado. Enviá un X-Carrito-Token válido.',
            ], 404);
        }

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Resumen de compra calculado correctamente.',
            'datos' => $this->carritoService->resumen($carrito),
        ]);
    }
}