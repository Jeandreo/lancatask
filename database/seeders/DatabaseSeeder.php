<?php

namespace Database\Seeders;

use App\Models\ProjectType;
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
            'password' => Hash::make('jean1010'),
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Analista de Marketing Digital',
            'created_by' => 1,
        ]);

        ProjectType::create([
            'name' => 'Lançamentos',
            'created_by' => 1,
        ]);

        ProjectType::create([
            'name' => 'Times',
            'created_by' => 1,
        ]);

        ProjectType::create([
            'name' => 'Gestão',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Gerente de Projetos',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Especialista em SEO',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Designer Gráfico',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Redator',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Analista de Mídia Paga',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Desenvolvedor Web',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Videomaker',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Copywriter',
            'created_by' => 1,
        ]);

        UserPosition::create([
            'name' => 'Analista de Dados',
            'created_by' => 1,
        ]);

    }
}
