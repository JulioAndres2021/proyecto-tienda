<?php

namespace App\Http\Controllers\Api\V1;


use App\DTOs\DatosCheckoutDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\DatosCheckoutRequest;
use App\Http\Resources\CarritoResource;
use App\Http\Resources\DatoCheckoutResource;
use App\Http\Resources\ResumenCompraResource;
use App\Http\Responses\ApiResponse;
use App\Models\Compra;
use App\Services\CarritoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CompraResource;
use App\Services\CheckoutService;

class CheckoutController extends Controller
{
    //Llama al servicio
    public function __construct(
        private CarritoService $carritoService,
        private CheckoutService $checkoutService,
    ) {}

    /*
    | revisar
    |-Revisa antes de iniciar la compra en carrito-
    */
    public function revisar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }

        $this->checkoutService->validarParaRevision(
            $carrito
        );

        $resumen = $this->carritoService->resumen(
            $carrito
        );

        return ApiResponse::success(
            [
                'carrito' => new CarritoResource($carrito),
                'resumen' => new ResumenCompraResource($resumen),
            ],
            'Carrito listo para continuar con la compra.'
        );
    }

    
    /*
    | registrarDatos
    |-Registra los datos del cliente y metodo de pago-
    */
    public function registrarDatos(DatosCheckoutRequest $request): JsonResponse 
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }

        if ($carrito->items()->count() === 0) {
            return ApiResponse::error(
                'El carrito está vacío.',
                422
            );
        }

        $dto = DatosCheckoutDTO::desdeArray(
            $request->validated()
        );

        $datosCheckout = $carrito->datosCheckout()->updateOrCreate(
            [
                'carrito_id' => $carrito->id,
            ],
            $dto->toArray()
        );

        return ApiResponse::success(
            new DatoCheckoutResource($datosCheckout),
            'Datos de checkout registrados correctamente.'
        );
    }

    /*
    | confirmar
    |-Registra la compra con todos los datos-
    */
    public function confirmar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return ApiResponse::error(
                'No se encontró el carrito.',
                404
            );
        }

        $compra = $this->checkoutService
            ->confirmar($carrito);

        return ApiResponse::success(
            new CompraResource($compra),
            'Compra confirmada correctamente.',
            201
        );
    }

    
}