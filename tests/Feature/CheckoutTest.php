<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function loginYObtenerToken(User $usuario): string
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $usuario->email,
            'password' => '12345678',
        ]);

        return $response->json('datos.autorizacion.token');
    }

    public function test_usuario_puede_completar_checkout_y_confirmar_compra(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Preparar usuario
        |--------------------------------------------------------------------------
        */

        $usuario = User::factory()->create(['password' => bcrypt('12345678'),]);

        /*
        |--------------------------------------------------------------------------
        | Preparar categoría
        |--------------------------------------------------------------------------
        */

        $categoria = Categoria::factory()->create([
            'usuario_id' => $usuario->id,
            'actualizado_por' => $usuario->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Preparar producto
        |--------------------------------------------------------------------------
        */

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'usuario_id' => $usuario->id,
            'actualizado_por' => $usuario->id,
            'precio' => 1000,
            'stock' => 10,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Login JWT
        |--------------------------------------------------------------------------
        */

        $jwt = $this->loginYObtenerToken($usuario);

        /*
        |--------------------------------------------------------------------------
        | Agregar producto al carrito
        |--------------------------------------------------------------------------
        */

        $agregar = $this
            ->withHeader(
                'Authorization',
                'Bearer '.$jwt
            )
            ->postJson(
                '/api/v1/carrito/productos',
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 2,
                ]
            );

        $agregar
            ->assertStatus(201)
            ->assertJsonPath('exito', true);

        $carritoToken = $agregar->json(
            'datos.token_carrito'
        );

        $this->assertNotEmpty(
            $carritoToken
        );

        /*
        |--------------------------------------------------------------------------
        | Revisar carrito
        |--------------------------------------------------------------------------
        */

        $revisar = $this
            ->withHeaders([
                'Authorization' =>
                    'Bearer '.$jwt,

                'X-Carrito-Token' =>
                    $carritoToken,
            ])
            ->getJson(
                '/api/v1/checkout/revisar'
            );

        $revisar
            ->assertStatus(200)
            ->assertJsonPath(
                'exito',
                true
            );

        /*
        |--------------------------------------------------------------------------
        | Registrar datos checkout
        |--------------------------------------------------------------------------
        */

        $datos = $this
            ->withHeaders([
                'Authorization' =>
                    'Bearer '.$jwt,

                'X-Carrito-Token' =>
                    $carritoToken,
            ])
            ->postJson(
                '/api/v1/checkout/datos',
                [
                    'nombre_cliente' => 'Cliente Test',
                    'email' => 'cliente@test.com',
                    'direccion_envio' => 'Calle Test 123',
                    'ciudad' => 'General Pico',
                    'codigo_postal' => '6360',
                    'metodo_pago' => 'efectivo',
                ]
            );

        $datos
            ->assertStatus(200)
            ->assertJsonPath(
                'exito',
                true
            );

        /*
        |--------------------------------------------------------------------------
        | Confirmar compra
        |--------------------------------------------------------------------------
        */

        $confirmar = $this
            ->withHeaders([
                'Authorization' =>
                    'Bearer '.$jwt,

                'X-Carrito-Token' =>
                    $carritoToken,
            ])
            ->postJson(
                '/api/v1/checkout/confirmar'
            );

        $confirmar
            ->assertStatus(201)
            ->assertJsonPath(
                'exito',
                true
            )
            ->assertJsonPath(
                'datos.estado',
                'confirmada'
            );

        /*
        |--------------------------------------------------------------------------
        | Verificar compra en base de datos
        |--------------------------------------------------------------------------
        */

        $this->assertDatabaseHas('compras',
            [
                'estado' => 'confirmada',
                'email' => 'cliente@test.com',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Verificar detalle
        |--------------------------------------------------------------------------
        */

        $this->assertDatabaseHas(
            'detalles_compra',
            [
                'producto_id' => $producto->id,
                'cantidad' => 2,
                'precio_unitario' => 1000,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Verificar descuento de stock
        |--------------------------------------------------------------------------
        */

        $producto->refresh();

        $this->assertEquals(8,$producto->stock);
    }

    /*
    |--------------------------------------------------------------------------
    | No confirmar checkout sin datos
    |--------------------------------------------------------------------------
    */

    public function test_no_permite_confirmar_sin_datos_checkout(): void
    {
        $usuario = User::factory()->create(['password' => bcrypt('12345678'),]);

        $categoria = Categoria::factory()->create(['usuario_id' => $usuario->id,]);

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'usuario_id' => $usuario->id,
            'stock' => 10,
            'precio' => 1000,
        ]);

        $jwt = $this->loginYObtenerToken($usuario);

        $agregar = $this
            ->withHeader(
                'Authorization',
                'Bearer '.$jwt
            )
            ->postJson(
                '/api/v1/carrito/productos',
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 1,
                ]
            );

        $carritoToken = $agregar->json(
            'datos.token_carrito'
        );

        /*
        | No ejecutamos /checkout/datos.
        | Intentamos confirmar directamente.
        */

        $response = $this
            ->withHeaders([
                'Authorization' =>
                    'Bearer '.$jwt,

                'X-Carrito-Token' =>
                    $carritoToken,
            ])
            ->postJson(
                '/api/v1/checkout/confirmar'
            );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'exito',
                false
            );

        /*
        | Además comprobamos que no se creó ninguna compra.
        */

        $this->assertDatabaseCount('compras',0);
    }

}
