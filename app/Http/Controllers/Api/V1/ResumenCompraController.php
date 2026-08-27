<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ResumenCompraResource;
use App\Http\Responses\ApiResponse;

use App\Http\Controllers\Controller;
use App\Services\CarritoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResumenCompraController extends Controller
{
    public function __construct(private CarritoService $carritoService) {
    }

    /*
    | mostrar
    |-muestra el resumen del carrito.-
    */
   public function mostrar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }
        
        /*
        Llama al método resumen() de CarritoService y le entrega el carrito actual.
        Ese método calcula:
        Subtotal
        Impuestos
        Costo de envío
        Total
        El resultado se almacena en $resumen y luego se transforma con ResumenCompraResource para enviarlo en la respuesta de la API:
        */
        $resumen = $this->carritoService->resumen($carrito);

        return ApiResponse::success(new ResumenCompraResource($resumen), 'Resumen de compra calculado correctamente.');
    }
}
