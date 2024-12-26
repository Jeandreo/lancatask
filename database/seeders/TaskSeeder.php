<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // // // Percorre todos os módulos e cria entre 2 e 5 tarefas para cada um
        // // Module::all()->each(function ($module) {
        // //     $taskCount = rand(2, 5); // Número aleatório de tarefas (2 a 5)

        // //     Task::factory()->count($taskCount)->create([
        // //         'module_id' => $module->id, // Associar a tarefa ao módulo atual
        // //     ]);
        // // });
    }
}
