<?php

namespace Tests\Feature;

use App\Contracts\PagoServiceInterface;
use App\Models\Carrito;
use App\Models\Categoria;
use App\Models\DatoCheckout;
use App\Models\Producto;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class PagoMockTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_no_confirma_si_pago_es_rechazado(): void
    {
        $usuario = User::factory()->create();

        $categoria = Categoria::factory()->create([
            'usuario_id' => $usuario->id,
        ]);

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'usuario_id' => $usuario->id,
            'stock' => 10,
            'precio' => 1000,
        ]);

        $carrito = Carrito::create([
            'token' => fake()->uuid(),
            'estado' => 'activo',
            'usuario_id' => $usuario->id,
        ]);

        $carrito->items()->create([
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => 1000,
        ]);

        $carrito->datosCheckout()->create([
            'nombre_cliente' => 'Cliente Test',
            'email' => 'cliente@test.com',
            'direccion_envio' => 'Calle Test 123',
            'ciudad' => 'General Pico',
            'codigo_postal' => '6360',
            'metodo_pago' => 'efectivo',
        ]);

        /*
        Crea un objeto simulado (mock) de PagoServiceInterface usando Mockery:
        $mock = Mockery::mock(PagoServiceInterface::class);
        Esto permite usar un sustituto del servicio real de pagos durante la prueba. Luego se configura su comportamiento:
        $mock->shouldReceive('aprobar')->once()->andReturn(false);
        Significa que aprobar() debe llamarse exactamente una vez y devolver false, simulando un pago rechazado sin realizar ningún pago real.
        */
        $mock = Mockery::mock(
            PagoServiceInterface::class
        );

        $mock
            ->shouldReceive('aprobar')
            ->once()
            ->andReturn(false);

        $this->app->instance(
            PagoServiceInterface::class,
            $mock
        );

        $service = app(CheckoutService::class);

        $this->expectException(
            ValidationException::class
        );

        $service->confirmar($carrito);
    }
}
