<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarritoTest extends TestCase
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

    public function test_usuario_puede_agregar_producto_al_carrito(): void
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

        $response = $this
            ->withHeader(
                'Authorization',
                'Bearer '.$jwt
            )
            ->postJson('/api/v1/carrito/productos',
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 2,
                ]
            );

        $response
            ->assertStatus(201)
            ->assertJsonPath('exito', true);

        $carritoToken = $response->json(
            'datos.token_carrito'
        );

        $this->assertNotEmpty($carritoToken);

        $this->assertDatabaseHas(
            'item_carritos',
            [
                'producto_id' => $producto->id,
                'cantidad' => 2,
            ]
        );
    }

    public function test_usuario_puede_eliminar_producto_del_carrito(): void
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
            ->postJson('/api/v1/carrito/productos',
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 2,
                ]
            );

        $carritoToken = $agregar->json('datos.token_carrito');

        $response = $this
            ->withHeaders([
                'Authorization' =>
                    'Bearer '.$jwt,

                'X-Carrito-Token' =>
                    $carritoToken,
            ])
            ->deleteJson(
                '/api/v1/carrito/productos/'
                .$producto->id
            );

        $response
            ->assertStatus(200)
            ->assertJsonPath('exito', true);

        $this->assertDatabaseMissing('item_carritos',
            [
                'producto_id' => $producto->id,
            ]
        );
    }
}
