<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\ProjectUser;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskParticipant;
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
            'password' => Hash::make('@Sucesso1243'),
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

        // // $project = Project::create([
        // //     'name' => '6em7 do João',
        // //     'type_id' => 1,
        // //     'created_by' => 1,
        // // ]);


        // // Status::create([
        // //     'name' => 'A Fazer',
        // //     'color' => '#009ef7',
        // //     'project_id' => $project->id,
        // //     'order' => 1,
        // //     'created_by' => 1,
        // // ]);

        // // Status::create([
        // //     'name' => 'Em andamento',
        // //     'color' => '#79bc17',
        // //     'project_id' => $project->id,
        // //     'order' => 2,
        // //     'created_by' => 1,
        // // ]);

        // // Status::create([
        // //     'name' => 'Concluído',
        // //     'color' => '#282c43',
        // //     'project_id' => $project->id,
        // //     'order' => 3,
        // //     'created_by' => 1,
        // // ]);

        // // ProjectUser::create([
        // //     'user_id' => 1,
        // //     'project_id' => 1,
        // // ]);

        // // ProjectUser::create([
        // //     'user_id' => 2,
        // //     'project_id' => 1,
        // // ]);

        // // Project::create([
        // //     'name' => 'Financias',
        // //     'type_id' => 3,
        // //     'created_by' => 1,
        // // ]);


        // // Status::create([
        // //     'name' => 'A Fazer',
        // //     'color' => '#009ef7',
        // //     'project_id' => 2,
        // //     'order' => 1,
        // //     'created_by' => 1,
        // // ]);

        // // Status::create([
        // //     'name' => 'Em andamento',
        // //     'color' => '#79bc17',
        // //     'project_id' => 2,
        // //     'order' => 2,
        // //     'created_by' => 1,
        // // ]);

        // // Status::create([
        // //     'name' => 'Concluído',
        // //     'color' => '#282c43',
        // //     'project_id' => 2,
        // //     'order' => 3,
        // //     'created_by' => 1,
        // // ]);

        // // ProjectUser::create([
        // //     'user_id' => 1,
        // //     'project_id' => 2,
        // // ]);

        // // ProjectUser::create([
        // //     'user_id' => 2,
        // //     'project_id' => 2,
        // // ]);

        // // // Módulo: Preparação para captação
        // // Module::create([
        // //     'name' => 'Preparação para captação',
        // //     'project_id' => 1,
        // //     'color' => '#674EA7',
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: INICIO DA CAPTAÇÃO
        // // Module::create([
        // //     'name' => 'INICIO DA CAPTAÇÃO',
        // //     'project_id' => 1,
        // //     'color' => '#674EA7',
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: Preparação para o evento
        // // Module::create([
        // //     'name' => 'Preparação para o evento',
        // //     'project_id' => 1,
        // //     'color' => '#85200C',
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: EVENTO NO AR
        // // Module::create([
        // //     'name' => 'EVENTO NO AR',
        // //     'project_id' => 1,
        // //     'color' => '#85200C',
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: Preparação do curso
        // // Module::create([
        // //     'name' => 'Preparação do curso',
        // //     'project_id' => 1,
        // //     'color' => randomColor(),
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: Preparação para venda
        // // Module::create([
        // //     'name' => 'Preparação para venda',
        // //     'project_id' => 1,
        // //     'color' => randomColor(),
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: Preparação curso Segredos
        // // Module::create([
        // //     'name' => 'Preparação curso Segredos',
        // //     'project_id' => 1,
        // //     'color' => randomColor(),
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: VENDAS
        // // Module::create([
        // //     'name' => 'VENDAS',
        // //     'project_id' => 1,
        // //     'color' => randomColor(),
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: Fechamento
        // // Module::create([
        // //     'name' => 'Fechamento',
        // //     'project_id' => 1,
        // //     'color' => randomColor(),
        // //     'created_by' => 1,
        // // ]);

        // // // Módulo: Entrega
        // //  Module::create([
        // //     'name' => 'Entrega',
        // //     'project_id' => 1,
        // //     'color' => randomColor(),
        // //     'created_by' => 1,
        // // ]);

        // // Task::factory(30)->create();

        // // $tasksCount = Task::count();

        // // for ($i=1; $i <= $tasksCount; $i++) {
        // //     TaskParticipant::create([
        // //         'user_id' => rand(1, 2),
        // //         'task_id' => $i,
        // //     ]);
        // // }

    }
}
