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
            'project_id' => 2,
        ]);

        ProjectUser::create([
            'user_id' => 2,
            'project_id' => 2,
        ]);

        // Módulo: Preparação para captação
        $module1 = Module::create([
            'name' => 'Preparação para captação',
            'project_id' => 1,
            'color' => '#674EA7',
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module1->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module1->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module1->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: INICIO DA CAPTAÇÃO
        $module2 = Module::create([
            'name' => 'INICIO DA CAPTAÇÃO',
            'project_id' => 1,
            'color' => '#674EA7',
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module2->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module2->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module2->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: Preparação para o evento
        $module3 = Module::create([
            'name' => 'Preparação para o evento',
            'project_id' => 1,
            'color' => '#85200C',
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module3->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module3->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module3->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: EVENTO NO AR
        $module4 = Module::create([
            'name' => 'EVENTO NO AR',
            'project_id' => 1,
            'color' => '#85200C',
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module4->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module4->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module4->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: Preparação do curso
        $module5 = Module::create([
            'name' => 'Preparação do curso',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module5->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module5->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module5->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: Preparação para venda
        $module6 = Module::create([
            'name' => 'Preparação para venda',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module6->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module6->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module6->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: Preparação curso Segredos
        $module7 = Module::create([
            'name' => 'Preparação curso Segredos',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module7->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module7->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module7->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: VENDAS
        $module8 = Module::create([
            'name' => 'VENDAS',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module8->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module8->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module8->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: Fechamento
        $module9 = Module::create([
            'name' => 'Fechamento',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module9->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module9->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module9->id,
            'order' => 3,
            'created_by' => 1,
        ]);

        // Módulo: Entrega
        $module10 = Module::create([
            'name' => 'Entrega',
            'project_id' => 1,
            'color' => randomColor(),
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'A Fazer',
            'color' => '#009ef7',
            'module_id' => $module10->id,
            'order' => 1,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Em andamento',
            'color' => '#79bc17',
            'module_id' => $module10->id,
            'order' => 2,
            'created_by' => 1,
        ]);

        Status::create([
            'name' => 'Concluído',
            'color' => '#282c43',
            'module_id' => $module10->id,
            'order' => 3,
            'created_by' => 1,
        ]);


        Task::factory(30)->create();

    }
}
