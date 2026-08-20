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
            return $this->carritoNoEncontrado();
        }

        $carrito->load('items.producto', 'datosCheckout');

        if ($carrito->items->isEmpty()) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'No se puede iniciar el checkout con el carrito vacío.',
            ], 422);
        }

        $sinStock = $carrito->items->filter(fn ($item) => !$item->producto || $item->cantidad > $item->producto->stock);
        $codigo = $sinStock->isEmpty() ? 200 : 422;

        return response()->json([
            'exito' => $sinStock->isEmpty(),
            'codigo' => $codigo,
            'mensaje' => $sinStock->isEmpty()
                ? 'Carrito listo para continuar con la compra.'
                : 'Hay productos sin stock suficiente.',
            'datos' => [
                'items' => $carrito->items,
                'resumen' => $this->carritoService->resumen($carrito),
                'datos_checkout' => $carrito->datosCheckout,
                'problemas_stock' => $sinStock->map(fn ($item) => [
                    'producto_id' => $item->producto_id,
                    'cantidad_solicitada' => $item->cantidad,
                    'stock_disponible' => $item->producto?->stock ?? 0,
                ])->values(),
            ],
        ], $codigo);
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
            return $this->carritoNoEncontrado();
        }

        $carrito->load('items.producto', 'datosCheckout');

        if ($carrito->items->isEmpty()) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'No se puede confirmar una compra con el carrito vacío.',
            ], 422);
        }

        if (!$carrito->datosCheckout) {
            return response()->json([
                'exito' => false,
                'codigo' => 422,
                'mensaje' => 'Primero debés registrar los datos de envío y pago.',
            ], 422);
        }

        foreach ($carrito->items as $item) {
            if (!$item->producto || $item->cantidad > $item->producto->stock) {
                return response()->json([
                    'exito' => false,
                    'codigo' => 422,
                    'mensaje' => 'No hay stock suficiente para confirmar la compra.',
                    'errores' => [
                        'stock' => ["Producto ID {$item->producto_id}: solicitado {$item->cantidad}, disponible ".($item->producto?->stock ?? 0).'.'],
                    ],
                ], 422);
            }
        }

        $resumen = $this->carritoService->resumen($carrito);

        try {
            $compra = DB::transaction(function () use ($carrito, $resumen) {
                $datosCheckout = $carrito->datosCheckout;
                $compra = Compra::create([
                    'carrito_id' => $carrito->id,
                    'nombre_cliente' => $datosCheckout->nombre_cliente,
                    'email' => $datosCheckout->email,
                    'direccion_envio' => $datosCheckout->direccion_envio,
                    'ciudad' => $datosCheckout->ciudad,
                    'codigo_postal' => $datosCheckout->codigo_postal,
                    'metodo_pago' => $datosCheckout->metodo_pago,
                    'subtotal' => $resumen['subtotal'],
                    'impuestos' => $resumen['impuestos'],
                    'costo_envio' => $resumen['costo_envio'],
                    'total' => $resumen['total'],
                    'estado' => 'confirmada',
                ]);

                foreach ($carrito->items as $item) {
                    $producto = $item->producto()->lockForUpdate()->first();

                    if (!$producto || $item->cantidad > $producto->stock) {
                        throw new RuntimeException('El stock cambió durante la confirmación de la compra.');
                    }

                    $compra->detalles()->create([
                        'producto_id' => $producto->id,
                        'nombre_producto' => $producto->nombre,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->precio_unitario,
                        'subtotal' => round($item->cantidad * (float) $item->precio_unitario, 2),
                    ]);

                    $producto->decrement('stock', $item->cantidad);
                }

                $carrito->items()->delete();
                $carrito->update(['estado' => 'comprado']);

                return $compra;
            });
        } catch (RuntimeException $e) {
            return response()->json([
                'exito' => false,
                'codigo' => 409,
                'mensaje' => $e->getMessage(),
            ], 409);
        }

        $respuesta = CompraConfirmadaDTO::desdeCompra($compra);

        return response()->json([
            'exito' => true,
            'codigo' => 201,
            'mensaje' => 'Compra confirmada correctamente.',
            'datos' => $respuesta->toArray(),
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