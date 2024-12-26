<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\ProjectUser;
use App\Models\Status;
use App\Models\Task;
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

        Project::create([
            'name' => '6em7 do João',
            'type_id' => 1,
            'created_by' => 1,
        ]);

        ProjectUser::create([
            'user_id' => 1,
            'project_id' => 1,
        ]);

        ProjectUser::create([
            'user_id' => 2,
            'project_id' => 1,
        ]);

        Project::create([
            'name' => 'Financias',
            'type_id' => 3,
            'created_by' => 1,
        ]);

        ProjectUser::create([
            'user_id' => 1,
            'project_id' => 1,
        ]);

        ProjectUser::create([
            'user_id' => 2,
            'project_id' => 1,
        ]);

        Module::create([
            'name' => 'Estratégia e definições',
            'project_id' => 1,
            'color' => '#A64D79',
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => 1,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => 1,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => 1,
            'order' => 1,
            'created_by' => 1,
        ]);


        Module::create([
            'name' => 'Preparação para captação',
            'project_id' => 1,
            'color' => '#674EA7',
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'INICIO DA CAPTAÇÃO',
            'project_id' => 1,
            'color' => '#674EA7',
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'Preparação para o evento',
            'project_id' => 1,
            'color' => '#85200C',
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'EVENTO NO AR',
            'project_id' => 1,
            'color' => '#85200C',
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'Preparação do curso',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'Preparação para venda',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'Preparação curso Segredos',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'VENDAS',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'Fechamento',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Module::create([
            'name' => 'Entrega',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Task::factory(20)->create();

    }
}
