<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductoFactory extends Factory
{
    use HasFactory;
    public function definition(): array
    {
        return [
            'sku' => fake()->unique()->bothify('PROD-###'),
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 100, 50000),
            'stock' => fake()->numberBetween(1, 100),
            'categoria_id' => Categoria::factory(),
            'usuario_id' => User::factory(),
            'actualizado_por' => null,
        ];
    }
}
