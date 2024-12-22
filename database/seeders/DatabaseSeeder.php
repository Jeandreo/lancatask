<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPosition;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'João Pedro',
            'role' => 'Administrador',
            'position_id' => 1,
            'email' => 'joaopedroottolini@gmail.com',
            'password' => Hash::make('Chicleteroxo@#25'),
            'created_by' => 1,
        ]);

        User::factory()->create([
            'name' => 'Jeandreo',
            'role' => 'Administrador',
            'position_id' => 1,
            'email' => 'jeandreofur@gmail.com',
            'password' => Hash::make('Inc@ns4v3l_2024'),
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Gestor de Contas',
            'created_by' => 1,
        ]);
        
    }
}
