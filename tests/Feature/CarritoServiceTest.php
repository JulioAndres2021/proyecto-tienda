<?php

namespace Tests\Feature;

use App\Models\Carrito;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use App\Services\CarritoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CarritoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_permite_agregar_cantidad_superior_al_stock(): void
    {
        $usuario = User::factory()->create();

        $categoria = Categoria::factory()->create(['usuario_id' => $usuario->id,]);

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'usuario_id' => $usuario->id,
            'stock' => 5,
            'precio' => 1000,
        ]);

        $carrito = Carrito::create([
            'token' => fake()->uuid(),
            'estado' => 'activo',
            'usuario_id' => $usuario->id,
        ]);

        $service = app(CarritoService::class);

        $this->expectException(ValidationException::class);

        $service->agregarProducto(
            $carrito,
            $producto,
            6
        );
    }

    public function test_agrega_producto_si_hay_stock_suficiente(): void
    {
        $usuario = User::factory()->create();

        $categoria = Categoria::factory()->create(['usuario_id' => $usuario->id,]);

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

        $service = app(CarritoService::class);

        $item = $service->agregarProducto(
            $carrito,
            $producto,
            3
        );

        $this->assertEquals(3, $item->cantidad);

        $this->assertEquals($producto->id, $item->producto_id);

        $this->assertDatabaseHas('item_carritos',
            [
                'carrito_id' => $carrito->id,
                'producto_id' => $producto->id,
                'cantidad' => 3,
            ]
        );
    }

    public function test_suma_cantidad_si_producto_ya_existe_en_carrito(): void
    {
        $usuario = User::factory()->create();

        $categoria = Categoria::factory()->create(['usuario_id' => $usuario->id,]);

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'usuario_id' => $usuario->id,
            'stock' => 20,
            'precio' => 1000,
        ]);

        $carrito = Carrito::create([
            'token' => fake()->uuid(),
            'estado' => 'activo',
            'usuario_id' => $usuario->id,
        ]);

        $service = app(CarritoService::class);

        $service->agregarProducto(
            $carrito,
            $producto,
            2
        );

        $item = $service->agregarProducto(
            $carrito,
            $producto,
            3
        );

        $this->assertEquals(5, $item->cantidad);

        $this->assertDatabaseHas('item_carritos',
            [
                'carrito_id' => $carrito->id,
                'producto_id' => $producto->id,
                'cantidad' => 5,
            ]
        );
    }

}
