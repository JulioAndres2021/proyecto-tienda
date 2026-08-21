<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\CompraConfirmadaDTO;
use App\DTOs\DatosCheckoutDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\DatosCheckoutRequest;
use App\Models\Compra;
use App\Models\DatoCheckout;
use App\Services\CarritoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(private CarritoService $carritoService) {}

    public function revisar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'No se encontró el carrito.',
            ], 404);
        }

        $carrito->load('items.producto');

        if ($carrito->items->isEmpty()) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'El carrito está vacío.',
            ], 422);
        }

        foreach ($carrito->items as $item) {
            if ($item->cantidad > $item->producto->stock) {
                return response()->json([
                    'exito' => false,
                    'codigo' => 422,
                    'mensaje' => 'Hay productos sin stock suficiente.',
                    'errores' => [
                        'stock' => [
                            "Stock insuficiente para {$item->producto->nombre}."
                        ],
                    ],
                ], 422);
            }
        }

        $resumen = $this->carritoService->resumen($carrito);

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Carrito listo para continuar con la compra.',
            'datos' => [
                'items' => $carrito->items,
                'resumen' => $resumen,
            ],
        ]);
    }

    public function registrarDatos(DatosCheckoutRequest $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return $this->carritoNoEncontrado();
        }

        $dto = DatosCheckoutDTO::desdeArray($request->validated());
        $datos = DatoCheckout::updateOrCreate(
            ['carrito_id' => $carrito->id],
            $dto->toArray()
        );

        return response()->json([
            'exito' => true,
            'codigo' => 200,
            'mensaje' => 'Datos de envío y pago registrados.',
            'datos' => $datos,
        ]);
    }

    public function confirmar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'No se encontró el carrito.',
            ], 404);
        }

        $carrito->load([
            'items.producto',
            'datosCheckout',
        ]);

        if ($carrito->items->isEmpty()) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'El carrito está vacío.',
            ], 422);
        }

        if (!$carrito->datosCheckout) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'Primero debe registrar los datos de checkout.',
            ], 422);
        }

        foreach ($carrito->items as $item) {
            if ($item->cantidad > $item->producto->stock) {
                return response()->json([
                    'exito' => false,
                    'codigo' => 422,
                    'mensaje' => 'Hay productos sin stock suficiente.',
                    'errores' => [
                        'stock' => [
                            "Stock insuficiente para {$item->producto->nombre}."
                        ],
                    ],
                ], 422);
            }
        }

        $resumen = $this->carritoService->resumen($carrito);

        $compra = DB::transaction(function () use (
            $carrito,
            $resumen
        ) {
            $datos = $carrito->datosCheckout;

            $compra = Compra::create([
                'carrito_id' => $carrito->id,
                'nombre_cliente' => $datos->nombre_cliente,
                'email' => $datos->email,
                'direccion_envio' => $datos->direccion_envio,
                'ciudad' => $datos->ciudad,
                'codigo_postal' => $datos->codigo_postal,
                'metodo_pago' => $datos->metodo_pago,
                'subtotal' => $resumen['subtotal'],
                'impuestos' => $resumen['impuestos'],
                'costo_envio' => $resumen['costo_envio'],
                'total' => $resumen['total'],
                'estado' => 'confirmada',
            ]);

            foreach ($carrito->items as $item) {
                $compra->detalles()->create([
                    'producto_id' => $item->producto->id,
                    'nombre_producto' => $item->producto->nombre,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'subtotal' => round(
                        $item->cantidad * (float) $item->precio_unitario,
                        2
                    ),
                ]);

                $item->producto->decrement(
                    'stock',
                    $item->cantidad
                );
            }

            $carrito->update([
                'estado' => 'comprado',
            ]);

            return $compra;
        });

        $dto = CompraConfirmadaDTO::desdeCompra($compra);

        return response()->json([
            'exito' => true,
            'codigo' => 201,
            'mensaje' => 'Compra confirmada correctamente.',
            'datos' => $dto->toArray(),
        ], 201);
    }

    private function carritoNoEncontrado(): JsonResponse
    {
        return response()->json([
            'exito' => false,
            'codigo' => 404,
            'mensaje' => 'Carrito no encontrado. Enviá un X-Carrito-Token válido.',
        ], 404);
    }
}