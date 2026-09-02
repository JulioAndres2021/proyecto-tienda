<?php

namespace Tests\Feature;

use App\Models\Carrito;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeguridadTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_no_puede_acceder_al_carrito_de_otro_usuario(): void
    {
        $usuarioA = User::factory()->create(['password' => bcrypt('12345678'),]);

        $usuarioB = User::factory()->create(['password' => bcrypt('12345678'),]);

        $carrito = Carrito::create([
            'token' => fake()->uuid(),
            'estado' => 'activo',
            'usuario_id' => $usuarioA->id,
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $usuarioB->email,
            'password' => '12345678',
        ]);

        $token = $login->json('datos.autorizacion.token');

        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer '.$token,
                'X-Carrito-Token' => $carrito->token,
            ])
            ->getJson('/api/v1/carrito');

        $response
            ->assertStatus(403)
            ->assertJson([
                'exito' => false,
                'codigo' => 403,
                'mensaje' => 'No tiene permisos para acceder a este carrito.',
            ]);
    }
}