<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categoria_id' => Categoria::inRandomOrder()->value('id'),

            'sku' => 'PROD-' . str_pad(
                $this->faker->unique()->numberBetween(1, 999),
                3,
                '0',
                STR_PAD_LEFT
            ),

            'nombre' => $this->faker->words(3, true),

            'descripcion' => $this->faker->sentence(),

            'precio' => $this->faker->randomFloat(2, 10, 1000),

            'stock' => $this->faker->numberBetween(0, 100),
        ];
    }
}