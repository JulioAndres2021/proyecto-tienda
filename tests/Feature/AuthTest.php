<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_exitoso_devuelve_token_jwt(): void
    {
        $usuario = User::factory()->create([
            'email' => 'usuario@test.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'usuario@test.com',
            'password' => '12345678',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('exito', true)
            ->assertJsonStructure([
                'exito',
                'codigo',
                'mensaje',
                'datos' => [
                    'usuario',
                    'autorizacion' => [
                        'token',
                        'tipo',
                        'expira_en',
                    ],
                ],
            ]);
    }

    public function test_login_con_credenciales_incorrectas_devuelve_422(): void
    {
        User::factory()->create([
            'email' => 'usuario@test.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'usuario@test.com',
            'password' => 'incorrecta',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('exito', false);
    }

    public function test_ruta_protegida_sin_token_devuelve_401_json(): void
    {
        $response = $this->get('/api/v1/productos');

        $response
            ->assertStatus(401)
            ->assertHeader('content-type', 'application/json')
            ->assertJson([
                'exito' => false,
                'codigo' => 401,
                'mensaje' => 'No autenticado.',
            ]);
    }

    public function test_ruta_protegida_con_token_valido_permite_acceso(): void
    {
        $usuario = User::factory()->create([
            'email' => 'usuario@test.com',
            'password' => bcrypt('12345678'),
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'usuario@test.com',
            'password' => '12345678',
        ]);

        $token = $login->json(
            'datos.autorizacion.token'
        );

        $response = $this
            ->withHeader(
                'Authorization',
                'Bearer '.$token
            )
            ->getJson('/api/v1/productos');

        $response
            ->assertStatus(200)
            ->assertJsonPath('exito', true);
    }
}
