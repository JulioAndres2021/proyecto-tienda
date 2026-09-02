<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
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

    public function test_usuario_autenticado_puede_crear_producto(): void
    {
        $usuario = User::factory()->create(['password' => bcrypt('12345678'),]);

        $categoria = Categoria::factory()->create(['usuario_id' => $usuario->id,]);

        $token = $this->loginYObtenerToken($usuario);

        $response = $this
            ->withHeader(
                'Authorization',
                'Bearer '.$token
            )
            ->postJson('/api/v1/productos', [
                'sku' => 'TEST-001',
                'nombre' => 'Producto Test',
                'descripcion' => 'Producto creado desde PHPUnit',
                'precio' => 1500,
                'stock' => 10,
                'categoria_id' => $categoria->id,
            ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('exito', true)
            ->assertJsonPath(
                'datos.nombre',
                'Producto Test'
            );

        $this->assertDatabaseHas('productos', [
            'sku' => 'TEST-001',
            'nombre' => 'Producto Test',
            'usuario_id' => $usuario->id,
        ]);
    }

    public function test_no_puede_crear_producto_sin_jwt(): void
    {
        $categoria = Categoria::factory()->create();

        $response = $this->postJson('/api/v1/productos',
            [
                'sku' => 'TEST-002',
                'nombre' => 'Producto sin auth',
                'descripcion' => 'No debería crearse',
                'precio' => 1000,
                'stock' => 5,
                'categoria_id' => $categoria->id,
            ]
        );

        $response
            ->assertStatus(401)
            ->assertJsonPath('exito', false);
    }
}