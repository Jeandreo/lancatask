<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\AgendaMember;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Contract;
use App\Models\Module;
use App\Models\ModuleOrder;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\ProjectUser;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskHistoric;
use App\Models\TaskParticipant;
use App\Models\User;
use App\Models\UserPosition;
use App\Models\UserPreferrence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = fake('pt_BR');

        // Usuário "sistema" para resolver dependência de FK created_by em users.
        $systemUser = User::create([
            'name' => 'Sistema',
            'role' => 'Administrador',
            'position_id' => 1,
            'email' => 'sistema@lancatask.local',
            'password' => Hash::make('password'),
            'created_by' => 1,
        ]);

        $positions = [
            'Administrador',
            'Gerente de Projetos',
            'Analista de Marketing Digital',
            'Especialista em SEO',
            'Designer Gráfico',
            'Redator',
            'Analista de Mídia Paga',
            'Desenvolvedor Web',
            'Videomaker',
            'Copywriter',
            'Analista de Dados',
        ];

        foreach ($positions as $position) {
            UserPosition::create([
                'name' => $position,
                'created_by' => $systemUser->id,
            ]);
        }

        $admin = User::factory()->create([
            'name' => 'João Pedro',
            'role' => 'Administrador',
            'position_id' => 1,
            'email' => 'joaopedroottolini@gmail.com',
            'password' => Hash::make('Chicleteroxo@#25'),
            'created_by' => $systemUser->id,
        ]);

        $secondAdmin = User::factory()->create([
            'name' => 'Jeandreo',
            'role' => 'Administrador',
            'position_id' => 1,
            'email' => 'jeandreofur@gmail.com',
            'password' => Hash::make('jean1010'),
            'created_by' => $admin->id,
        ]);

        $teamUsers = User::factory(6)->create([
            'created_by' => $admin->id,
            'role' => 'Usuário',
            'position_id' => 2,
        ])->each(function (User $user) {
            $user->update([
                'role' => fake()->randomElement(['Gerente', 'Usuário']),
                'position_id' => fake()->numberBetween(2, 11),
            ]);
        });

        $allUsers = collect([$admin, $secondAdmin])->merge($teamUsers)->values();

        $projectTypes = collect([
            'Lançamentos',
            'Times',
            'Gestão',
        ])->map(fn (string $name) => ProjectType::create([
            'name' => $name,
            'created_by' => $admin->id,
        ]));

        $projectsData = [
            ['name' => 'Lançamento Evergreen 2026', 'type_index' => 0, 'type_is' => 'time'],
            ['name' => 'Operação Comercial', 'type_index' => 1, 'type_is' => 'time'],
            ['name' => 'Rotina Pessoal', 'type_index' => 2, 'type_is' => 'pessoal'],
        ];

        $projects = collect($projectsData)->map(function (array $projectData) use ($projectTypes, $admin, $faker) {
            return Project::create([
                'name' => $projectData['name'],
                'type_id' => $projectTypes[$projectData['type_index']]->id,
                'type_is' => $projectData['type_is'],
                'description' => $faker->sentence(),
                'start' => now()->subDays(rand(10, 45)),
                'end' => now()->addDays(rand(30, 120)),
                'created_by' => $admin->id,
            ]);
        });

        foreach ($projects as $project) {
            foreach ($allUsers as $user) {
                ProjectUser::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                ]);
            }

            $statuses = collect([
                ['name' => 'A Fazer', 'color' => '#009ef7', 'order' => 1, 'done' => false],
                ['name' => 'Em andamento', 'color' => '#79bc17', 'order' => 2, 'done' => false],
                ['name' => 'Concluído', 'color' => '#282c43', 'order' => 3, 'done' => true],
            ])->map(fn (array $status) => Status::create([
                'name' => $status['name'],
                'color' => $status['color'],
                'project_id' => $project->id,
                'order' => $status['order'],
                'done' => $status['done'],
                'created_by' => $admin->id,
            ]));

            $modules = collect([
                ['name' => 'Planejamento', 'color' => '#674EA7'],
                ['name' => 'Execução', 'color' => '#0B5394'],
                ['name' => 'Pós-venda', 'color' => '#38761D'],
            ])->map(function (array $moduleData, int $index) use ($project, $admin) {
                return Module::create([
                    'name' => $moduleData['name'],
                    'project_id' => $project->id,
                    'color' => $moduleData['color'],
                    'order' => $index + 1,
                    'created_by' => $admin->id,
                ]);
            });

            foreach ($allUsers as $user) {
                foreach ($modules as $module) {
                    ModuleOrder::create([
                        'order' => $module->order,
                        'user_id' => $user->id,
                        'module_id' => $module->id,
                        'project_id' => $project->id,
                    ]);
                }
            }

            foreach ($modules as $module) {
                $tasks = Task::factory(rand(4, 7))->create([
                    'module_id' => $module->id,
                    'status_id' => $statuses->random()->id,
                    'created_by' => $admin->id,
                ]);

                foreach ($tasks as $task) {
                    $participants = $allUsers->random(rand(1, min(3, $allUsers->count())));

                    foreach ($participants as $participant) {
                        TaskParticipant::create([
                            'user_id' => $participant->id,
                            'task_id' => $task->id,
                        ]);
                    }

                    Comment::create([
                        'task_id' => $task->id,
                        'text' => $faker->sentence(12),
                        'created_by' => $participants->first()->id,
                    ]);

                    TaskHistoric::create([
                        'task_id' => $task->id,
                        'action' => 'created',
                        'previous_key' => null,
                        'key' => (string) $task->status_id,
                        'created_by' => $admin->id,
                    ]);
                }
            }
        }

        $contracts = collect([
            'Plano Mensal',
            'Plano Trimestral',
            'Plano Anual',
        ])->map(fn (string $name) => Contract::create([
            'name' => $name,
            'created_by' => $admin->id,
        ]));

        $clients = collect(range(1, 6))->map(function () use ($faker, $contracts, $admin) {
            return Client::create([
                'name' => $faker->name(),
                'person_type' => $faker->randomElement(['PF', 'PJ']),
                'document' => $faker->numerify('###########'),
                'email' => $faker->unique()->safeEmail(),
                'contract_id' => $contracts->random()->id,
                'contract_value' => (string) $faker->randomFloat(2, 997, 9997),
                'phone' => $faker->numerify('(##) #####-####'),
                'start_date' => now()->subDays(rand(1, 120)),
                'end_date' => now()->addDays(rand(30, 365)),
                'zip' => $faker->numerify('#####-###'),
                'street' => $faker->streetName(),
                'number' => (string) $faker->numberBetween(1, 9999),
                'complement' => $faker->optional()->secondaryAddress(),
                'neighborhood' => $faker->citySuffix(),
                'city' => $faker->city(),
                'state' => $faker->stateAbbr(),
                'observations' => $faker->optional()->paragraph(),
                'created_by' => $admin->id,
            ]);
        });

        $agendas = collect([
            ['name' => 'Reunião semanal de time', 'color' => '#0ea5e9', 'general' => true],
            ['name' => 'Call com clientes', 'color' => '#22c55e', 'general' => false],
        ])->map(function (array $agendaData) use ($admin) {
            return Agenda::create([
                'name' => $agendaData['name'],
                'description' => 'Agenda criada automaticamente no seeder para testes.',
                'general' => $agendaData['general'],
                'date_start' => now()->toDateString(),
                'date_end' => now()->addMonth()->toDateString(),
                'hour_start' => '09:00:00',
                'hour_end' => '10:00:00',
                'recurrent' => true,
                'frequency' => 'weekly',
                'week_days' => '1,3,5',
                'duration' => '01:00:00',
                'color' => $agendaData['color'],
                'created_by' => $admin->id,
            ]);
        });

        foreach ($agendas as $agenda) {
            foreach ($allUsers->random(3) as $member) {
                AgendaMember::create([
                    'type' => 'user',
                    'member_id' => $member->id,
                    'agenda_id' => $agenda->id,
                ]);
            }

            AgendaMember::create([
                'type' => 'client',
                'member_id' => $clients->random()->id,
                'agenda_id' => $agenda->id,
            ]);
        }

        foreach ($allUsers as $user) {
            UserPreferrence::create([
                'type' => 'sidebarGroupOrder',
                'value' => (string) $projectTypes->random()->id,
                'created_by' => $user->id,
            ]);

            UserPreferrence::create([
                'type' => 'sidebarProjectsOrder',
                'value' => (string) $projects->random()->id,
                'created_by' => $user->id,
            ]);
        }
    }
}
