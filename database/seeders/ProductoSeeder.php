<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = Categoria::all();

        Producto::factory()
            ->count(50)
            ->create()
            ->each(function ($producto) use ($categorias) {
                $producto->update([
                    'categoria_id' => $categorias->random()->id,
                ]);
            });
    }
}