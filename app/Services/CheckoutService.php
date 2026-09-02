<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Compra;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Contracts\PagoServiceInterface;

class CheckoutService
{
    public function __construct(private CarritoService $carritoService,private PagoServiceInterface $pagoService)
    {}



    public function confirmar(Carrito $carrito): Compra
    {
        $this->validarParaConfirmar($carrito);

        $resumen = $this->carritoService->resumen($carrito);

        return DB::transaction(function () use (
            $carrito,
            $resumen
        ) {
            $datos = $carrito->datosCheckout;

            $aprobado = $this->pagoService->aprobar((float) $resumen['total'],
                $carrito->datosCheckout->metodo_pago
            );

            if (!$aprobado) {
                throw ValidationException::withMessages([
                    'pago' => [
                        'El pago fue rechazado.',
                    ],
                ]);
            }

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

            return $compra->load('detalles');
        });
    }

    public function validarParaRevision(Carrito $carrito): void
    {
        $carrito->load('items.producto');

        $this->validarItems($carrito);
        $this->validarStock($carrito);
    }

    public function validarParaConfirmar(Carrito $carrito): void
    {
        $carrito->load([
            'items.producto',
            'datosCheckout',
        ]);

        $this->validarItems($carrito);
        $this->validarDatosCheckout($carrito);
        $this->validarStock($carrito);
    }

    private function validarItems(Carrito $carrito): void
    {
        if ($carrito->items->isEmpty()) {
            throw ValidationException::withMessages([
                'carrito' => [
                    'El carrito está vacío.',
                ],
            ]);
        }
    }

    private function validarDatosCheckout(Carrito $carrito): void
    {
        if (!$carrito->datosCheckout) {
            throw ValidationException::withMessages([
                'checkout' => [
                    'Primero debe registrar los datos de checkout.',
                ],
            ]);
        }
    }

    private function validarStock(Carrito $carrito): void
    {
        foreach ($carrito->items as $item) {
            if ($item->cantidad > $item->producto->stock) {
                throw ValidationException::withMessages([
                    'stock' => [
                        "Stock insuficiente para {$item->producto->nombre}.",
                    ],
                ]);
            }
        }
    }
}
