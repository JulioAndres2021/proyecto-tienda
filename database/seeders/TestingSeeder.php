<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(3)
            ->create()
            ->each(function (User $usuario) {

                Categoria::factory()
                    ->count(2)
                    ->create([
                        'usuario_id' => $usuario->id,
                    ])
                    ->each(function (Categoria $categoria) use ($usuario) {

                        Producto::factory()
                            ->count(5)
                            ->create([
                                'categoria_id' => $categoria->id,
                                'usuario_id' => $usuario->id,
                            ]);
                    });
            });
    }
}
