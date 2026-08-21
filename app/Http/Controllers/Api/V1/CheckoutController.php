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


class CheckoutController extends Controller
{
    //Llama al servicio
    public function __construct(private CarritoService $carritoService) {}

    /*
    | revisar
    |-Revisa antes de iniciar la compra en carrito-
    */
    public function revisar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);//obtenemos el token

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'No se encontró el carrito.',
            ], 404);
        }

        $carrito->load('items.producto');//Carga las relaciones del carrito

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

        /*
        Llama al método resumen() del servicio CarritoService y le pasa el carrito actual.
        Ese método calcula:
        subtotal
        impuestos
        costo_envio
        total
        */
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

    /*
    | registrarDatos
    |-Registra los datos del cliente y metodo de pago-
    */
    public function registrarDatos(DatosCheckoutRequest $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);//obtenemos el token

        //si no está el carrito, llama al método y muestra mensaje
        if (!$carrito) {
            return $this->carritoNoEncontrado();
        }

        /*
        Valida yguarda los datos del chekout
        $request->validated() obtiene únicamente los datos que pasaron las reglas de validación.
        DatosCheckoutDTO::desdeArray(...) convierte esos datos en un objeto DTO, con una estructura controlada.
        */
        $dto = DatosCheckoutDTO::desdeArray($request->validated());

        /*
        Busca un registro de checkout asociado a ese carrito:
        Si existe, lo actualiza.
        Si no existe, lo crea.
        $dto->toArray() convierte el DTO en un arreglo para guardar sus datos.
        El resultado se guarda en $datos, que contiene los datos de envío y pago registrados.
        */
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

    /*
    | confirmar
    |-Registra la compra con todos los datos-
    */
    public function confirmar(Request $request): JsonResponse
    {
        $carrito = $this->carritoService->obtener($request);//obtenemos el token

        if (!$carrito) {
            return response()->json([
                'exito' => false,
                'codigo' => 404,
                'mensaje' => 'No se encontró el carrito.',
            ], 404);
        }

        /*
        Carga desde la base de datos las relaciones necesarias del carrito:
        items.producto: carga los artículos del carrito y el producto correspondiente de cada artículo.
        datosCheckout: carga los datos de envío y pago asociados al carrito.
        */
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

        /*
        Llama al método resumen() de CarritoService y le pasa el carrito actual.
        Ese método calcula y devuelve:
        Subtotal
        Impuestos
        Costo de envío
        Total
        El resultado se guarda en $resumen para usarlo al crear la compra:
        */
        $resumen = $this->carritoService->resumen($carrito);

        /*
        Este código crea y guarda la compra dentro de una transacción de base de datos
        DB::transaction() ejecuta las operaciones de forma segura.
        use ($carrito, $resumen) permite usar esas variables dentro de la función.
        $datos = $carrito->datosCheckout obtiene los datos del cliente, envío y pago.
        */
        $compra = DB::transaction(function () use (
            $carrito,
            $resumen
        ) {
            $datos = $carrito->datosCheckout;
            /*Crea un registro en la tabla compras con: El carrito asociado, Los datos del cliente y envío, Subtotal, impuestos, envío y total y Estado confirmada.
            Finalmente, $compra contiene la compra recién creada. Si alguna operación posterior de la transacción falla, Laravel revierte los cambios realizados. */
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
                
                /*
                Este código reduce el stock del producto después de confirmar la compra:
                $item->producto obtiene el producto asociado al artículo del carrito.
                decrement('stock', ...) resta una cantidad directamente en la base de datos.
                $item->cantidad indica cuántas unidades se compraron.
                */
                $item->producto->decrement(
                    'stock',
                    $item->cantidad
                );
            }
            
            /*actualiza el estado del carrito a "comprado" */
            $carrito->update([
                'estado' => 'comprado',
            ]);

            return $compra;
        });

        /*
        Convierte la compra recién creada en un objeto CompraConfirmadaDTO.
        CompraConfirmadaDTO define qué datos de la compra se van a devolver.
        desdeCompra($compra) toma la información del modelo $compra.
        El resultado se guarda en $dto.
        Después se convierte en arreglo para enviarlo como JSON:
        */
        $dto = CompraConfirmadaDTO::desdeCompra($compra);

        return response()->json([
            'exito' => true,
            'codigo' => 201,
            'mensaje' => 'Compra confirmada correctamente.',
            'datos' => $dto->toArray(),
        ], 201);
    }

    /*
    | carritoNoEncontrado
    |-Metodo mostrar que no se encontró el token del carrito-
    */
    private function carritoNoEncontrado(): JsonResponse
    {
        return response()->json([
            'exito' => false,
            'codigo' => 404,
            'mensaje' => 'Carrito no encontrado. Enviá un X-Carrito-Token válido.',
        ], 404);
    }
}