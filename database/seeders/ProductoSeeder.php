<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //creamos un producto de ejemplo
        Producto::create([
            'nombre' => 'Teléfono inteligente',
            'descripcion' => 'Un teléfono inteligente de última generación.',
            'categoria_id' => 1, // Asignamos la categoría de Electrónica
            'precio' => 699.99,
            'stock' => 50,
        ]);
        //creamos otro producto de ejemplo
        Producto::create([
            'nombre' => 'Camiseta de algodón',
            'descripcion' => 'Camiseta cómoda y ligera de algodón.',
            'categoria_id' => 2, // Asignamos la categoría de Ropa
            'precio' => 19.99,
            'stock' => 200,
        ]);
    }
}