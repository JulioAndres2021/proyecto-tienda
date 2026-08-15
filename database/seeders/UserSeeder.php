<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creamos el usuario administrador con los datos proporcionados
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'), // Cambia 'password' por la contraseña que desees
        ]);

        //Creamos el cliente con los datos proporcionados
        User::create([
            'name' => 'Cliente 1',
            'email' => 'cliente1@example.com',
            'password' => bcrypt('12345678'),
        ]);
    }
}