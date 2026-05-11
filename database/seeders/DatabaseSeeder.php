<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\AgendaMember;
use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Comment;
use App\Models\Contract;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\FinancialWallet;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $this->clearDomainTables();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Sistema',
            'role' => 'Administrador',
            'position_id' => 1,
            'email' => 'sistema@lancatask.local',
            'email_verified_at' => $now,
            'password' => Hash::make('password'),
            'sidebar' => true,
            'sounds' => true,
            'status' => true,
            'created_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $systemUser = User::find(1);

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

        $admin = User::create([
            'name' => 'João Pedro',
            'role' => 'Administrador',
            'position_id' => 1,
            'email' => 'joaopedroottolini@gmail.com',
            'email_verified_at' => $now,
            'password' => Hash::make('Chicleteroxo@#25'),
            'created_by' => $systemUser->id,
        ]);

        $secondAdmin = User::create([
            'name' => 'Jeandreo',
            'role' => 'Administrador',
            'position_id' => 1,
            'email' => 'jeandreofur@gmail.com',
            'email_verified_at' => $now,
            'password' => Hash::make('jean1010'),
            'created_by' => $admin->id,
        ]);

        $teamUsers = [];
        $teamUsers[] = User::create([
            'name' => 'Maria Eduarda',
            'role' => 'Gerente',
            'position_id' => 2,
            'email' => 'maria@lancatask.local',
            'email_verified_at' => $now,
            'password' => Hash::make('password'),
            'created_by' => $admin->id,
        ]);
        $teamUsers[] = User::create([
            'name' => 'Carlos Henrique',
            'role' => 'Gerente',
            'position_id' => 3,
            'email' => 'carlos@lancatask.local',
            'email_verified_at' => $now,
            'password' => Hash::make('password'),
            'created_by' => $admin->id,
        ]);
        $teamUsers[] = User::create([
            'name' => 'Ana Paula',
            'role' => 'Usuário',
            'position_id' => 4,
            'email' => 'ana@lancatask.local',
            'email_verified_at' => $now,
            'password' => Hash::make('password'),
            'created_by' => $admin->id,
        ]);
        $teamUsers[] = User::create([
            'name' => 'Lucas Martins',
            'role' => 'Usuário',
            'position_id' => 8,
            'email' => 'lucas@lancatask.local',
            'email_verified_at' => $now,
            'password' => Hash::make('password'),
            'created_by' => $admin->id,
        ]);
        $teamUsers[] = User::create([
            'name' => 'Bruna Costa',
            'role' => 'Usuário',
            'position_id' => 9,
            'email' => 'bruna@lancatask.local',
            'email_verified_at' => $now,
            'password' => Hash::make('password'),
            'created_by' => $admin->id,
        ]);

        $allUsers = collect([
            $systemUser,
            $admin,
            $secondAdmin,
        ]);
        foreach ($teamUsers as $teamUser) {
            $allUsers->push($teamUser);
        }

        $wallets = $this->seedFinancialWallets($admin);
        $categories = $this->seedFinancialCategories($admin);
        $contracts = $this->seedContracts($admin, $wallets, $categories);
        $clients = $this->seedClients($admin);
        $clientContracts = $this->seedClientContracts($admin, $clients, $contracts);
        $projects = $this->seedProjects($admin, $allUsers, $clients);

        $this->seedFinancialTransactions($admin, $allUsers, $clients, $clientContracts, $wallets, $categories);
        $this->seedSessions($allUsers);
        $this->seedAgendas($admin, $allUsers, $clients);
        $this->seedSidebarPreferences($allUsers, $projects);
    }

    private function clearDomainTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'agendas_member',
            'agendas',
            'comments',
            'financial_transactions',
            'client_contracts',
            'clients',
            'contracts',
            'financial_categories',
            'financial_wallets',
            'modules_order',
            'projects_users',
            'tasks_historics',
            'tasks_participants',
            'tasks',
            'statuses',
            'modules',
            'projects',
            'projects_types',
            'user_preferrences',
            'sessions',
            'users_positions',
            'users',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedFinancialWallets(User $admin)
    {
        $walletNames = [
            'Carteira Padrão',
            'Conta Corrente',
            'Reserva',
            'Cartão Corporativo',
        ];

        $wallets = collect();

        foreach ($walletNames as $walletName) {
            $wallet = FinancialWallet::create([
                'name' => $walletName,
                'status' => true,
                'created_by' => $admin->id,
            ]);
            $wallets->put($walletName, $wallet);
        }

        return $wallets;
    }

    private function seedFinancialCategories(User $admin)
    {
        $categoryData = [
            ['name' => 'Receita Recorrente de Contrato', 'type' => 'entrada'],
            ['name' => 'Receita Avulsa', 'type' => 'entrada'],
            ['name' => 'Consultoria', 'type' => 'entrada'],
            ['name' => 'Tráfego Pago', 'type' => 'debito'],
            ['name' => 'Ferramentas', 'type' => 'debito'],
            ['name' => 'Equipe/Freelancer', 'type' => 'debito'],
            ['name' => 'Infraestrutura', 'type' => 'debito'],
        ];

        $categories = collect();

        foreach ($categoryData as $categoryItem) {
            $category = FinancialCategory::create([
                'name' => $categoryItem['name'],
                'type' => $categoryItem['type'],
                'status' => true,
                'created_by' => $admin->id,
            ]);
            $categories->put($category->name, $category);
        }

        return $categories;
    }

    private function seedContracts(User $admin, $wallets, $categories)
    {
        $contractData = [
            ['name' => 'Plano Starter', 'amount' => 1200],
            ['name' => 'Plano Growth', 'amount' => 3500],
            ['name' => 'Plano Scale', 'amount' => 7800],
            ['name' => 'Plano Enterprise', 'amount' => 12500],
        ];

        $contracts = collect();

        foreach ($contractData as $contractItem) {
            $contract = Contract::create([
                'name' => $contractItem['name'],
                'period_in_months' => 1,
                'duration_in_months' => null,
                'is_open_ended' => true,
                'wallet_id' => $wallets->get('Conta Corrente')->id,
                'category_id' => $categories->get('Receita Recorrente de Contrato')->id,
                'status' => true,
                'created_by' => $admin->id,
            ]);

            $contract->seed_amount = $contractItem['amount'];
            $contracts->push($contract);
        }

        return $contracts;
    }

    private function seedClients(User $admin)
    {
        $clientData = [
            ['name' => 'Aurora Digital Ltda', 'document' => '11222333000144', 'email' => 'financeiro@auroradigital.local', 'city' => 'Curitiba', 'state' => 'PR'],
            ['name' => 'Clínica Bela Vita', 'document' => '22333444000155', 'email' => 'contato@belavita.local', 'city' => 'São Paulo', 'state' => 'SP'],
            ['name' => 'Escola Novos Passos', 'document' => '33444555000166', 'email' => 'admin@novospassos.local', 'city' => 'Florianópolis', 'state' => 'SC'],
            ['name' => 'Instituto Horizonte', 'document' => '44555666000177', 'email' => 'gestao@horizonte.local', 'city' => 'Porto Alegre', 'state' => 'RS'],
            ['name' => 'Loja Casa Norte', 'document' => '55666777000188', 'email' => 'financeiro@casanorte.local', 'city' => 'Belo Horizonte', 'state' => 'MG'],
            ['name' => 'Mentoria Alfa', 'document' => '66777888000199', 'email' => 'suporte@mentoriaalfa.local', 'city' => 'Rio de Janeiro', 'state' => 'RJ'],
        ];

        $clients = collect();

        foreach ($clientData as $index => $clientItem) {
            $client = Client::create([
                'name' => $clientItem['name'],
                'person_type' => 'PJ',
                'document' => $clientItem['document'],
                'email' => $clientItem['email'],
                'website' => 'https://example.com',
                'instagram' => '@' . strtolower(str_replace(' ', '', $clientItem['name'])),
                'phone' => '(41) 99999-10' . str_pad($index, 2, '0', STR_PAD_LEFT),
                'payment_day' => 10 + $index,
                'start_date' => now()->subMonths(6 - $index)->startOfMonth(),
                'end_date' => null,
                'zip' => '80000-000',
                'street' => 'Rua das Operações',
                'number' => 100 + $index,
                'complement' => 'Sala ' . (200 + $index),
                'neighborhood' => 'Centro',
                'city' => $clientItem['city'],
                'state' => $clientItem['state'],
                'observations' => 'Cliente criado pelo seeder oficial do ambiente local.',
                'status' => true,
                'created_by' => $admin->id,
            ]);

            $clients->push($client);
        }

        return $clients;
    }

    private function seedClientContracts(User $admin, $clients, $contracts)
    {
        $clientContracts = collect();

        foreach ($clients as $index => $client) {
            $contract = $contracts->get($index % $contracts->count());

            $clientContract = ClientContract::create([
                'client_id' => $client->id,
                'contract_id' => $contract->id,
                'amount' => $contract->seed_amount,
                'start_date' => now()->subMonths(5 - $index)->startOfMonth(),
                'end_date' => null,
                'period_in_months' => 1,
                'duration_in_months' => null,
                'status' => true,
                'created_by' => $admin->id,
            ]);

            $clientContracts->push($clientContract);
        }

        return $clientContracts;
    }

    private function seedProjects(User $admin, $allUsers, $clients)
    {
        $projectTypes = collect();
        $projectTypeNames = [
            'Lançamentos',
            'Times',
            'Gestão',
        ];

        foreach ($projectTypeNames as $projectTypeName) {
            $projectType = ProjectType::create([
                'name' => $projectTypeName,
                'status' => true,
                'created_by' => $admin->id,
            ]);
            $projectTypes->put($projectTypeName, $projectType);
        }

        $projectData = [
            ['name' => 'Lançamento Evergreen 2026', 'type' => 'Lançamentos', 'type_is' => 'time', 'client_index' => 0],
            ['name' => 'Operação Comercial', 'type' => 'Times', 'type_is' => 'time', 'client_index' => 1],
            ['name' => 'Implantação CRM', 'type' => 'Gestão', 'type_is' => 'time', 'client_index' => 2],
            ['name' => 'Rotina Pessoal', 'type' => 'Gestão', 'type_is' => 'pessoal', 'client_index' => null],
        ];

        $projects = collect();

        foreach ($projectData as $index => $projectItem) {
            $clientId = null;
            if ($projectItem['client_index'] !== null) {
                $clientId = $clients->get($projectItem['client_index'])->id;
            }

            $project = Project::create([
                'name' => $projectItem['name'],
                'type_id' => $projectTypes->get($projectItem['type'])->id,
                'type_is' => $projectItem['type_is'],
                'client_id' => $clientId,
                'description' => 'Projeto criado pelo seeder para demonstração operacional.',
                'start' => now()->subDays(30 + ($index * 7)),
                'end' => now()->addDays(45 + ($index * 12)),
                'status' => true,
                'created_by' => $admin->id,
            ]);

            $projects->push($project);
            $this->seedProjectStructure($admin, $allUsers, $project, $index);
        }

        return $projects;
    }

    private function seedProjectStructure(User $admin, $allUsers, Project $project, int $projectIndex): void
    {
        foreach ($allUsers as $user) {
            ProjectUser::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);
        }

        $statusData = [
            ['name' => 'A Fazer', 'color' => '#009EF7', 'order' => 1, 'done' => false],
            ['name' => 'Em andamento', 'color' => '#79BC17', 'order' => 2, 'done' => false],
            ['name' => 'Concluído', 'color' => '#282C43', 'order' => 3, 'done' => true],
        ];

        $statuses = collect();
        foreach ($statusData as $statusItem) {
            $status = Status::create([
                'name' => $statusItem['name'],
                'color' => $statusItem['color'],
                'project_id' => $project->id,
                'order' => $statusItem['order'],
                'done' => $statusItem['done'],
                'status' => true,
                'created_by' => $admin->id,
            ]);
            $statuses->put($status->name, $status);
        }

        $moduleData = [
            ['name' => 'Planejamento', 'color' => '#674EA7'],
            ['name' => 'Execução', 'color' => '#0B5394'],
            ['name' => 'Pós-venda', 'color' => '#38761D'],
        ];

        foreach ($moduleData as $moduleIndex => $moduleItem) {
            $module = Module::create([
                'name' => $moduleItem['name'],
                'project_id' => $project->id,
                'color' => $moduleItem['color'],
                'order' => $moduleIndex + 1,
                'status' => true,
                'created_by' => $admin->id,
            ]);

            foreach ($allUsers as $user) {
                ModuleOrder::create([
                    'order' => $module->order,
                    'user_id' => $user->id,
                    'module_id' => $module->id,
                    'project_id' => $project->id,
                ]);
            }

            $this->seedTasks($admin, $allUsers, $statuses, $module, $projectIndex, $moduleIndex);
        }
    }

    private function seedTasks(User $admin, $allUsers, $statuses, Module $module, int $projectIndex, int $moduleIndex): void
    {
        $taskNames = [
            'Definir cronograma macro',
            'Revisar briefing do cliente',
            'Criar materiais da campanha',
            'Configurar automações',
            'Validar página de vendas',
            'Preparar relatório semanal',
        ];

        foreach ($taskNames as $taskIndex => $taskName) {
            $checked = $taskIndex === 5;
            $status = $checked
                ? $statuses->get('Concluído')
                : $statuses->get($taskIndex % 2 === 0 ? 'A Fazer' : 'Em andamento');

            $task = Task::create([
                'module_id' => $module->id,
                'status_id' => $status->id,
                'checked' => $checked,
                'checked_at' => $checked ? now()->subDays($taskIndex) : null,
                'order' => $taskIndex + 1,
                'priority' => $taskIndex % 3,
                'date' => now()->addDays($taskIndex - 2),
                'date_start' => now()->addDays($taskIndex - 2)->toDateString(),
                'date_end' => now()->addDays($taskIndex + 3)->toDateString(),
                'name' => $taskName . ' - ' . $module->name,
                'description' => 'Tarefa de demonstração criada pelo seeder.',
                'status' => true,
                'created_by' => $admin->id,
                'updated_at' => now()->subHours(($projectIndex * 8) + ($moduleIndex * 2) + $taskIndex),
            ]);

            $firstParticipant = $allUsers->get(($taskIndex + $moduleIndex) % $allUsers->count());
            $secondParticipant = $allUsers->get(($taskIndex + $moduleIndex + 1) % $allUsers->count());

            TaskParticipant::create([
                'user_id' => $firstParticipant->id,
                'task_id' => $task->id,
            ]);
            TaskParticipant::create([
                'user_id' => $secondParticipant->id,
                'task_id' => $task->id,
            ]);

            Comment::create([
                'task_id' => $task->id,
                'text' => 'Comentário inicial gerado para demonstrar o histórico da tarefa.',
                'created_by' => $firstParticipant->id,
            ]);

            TaskHistoric::create([
                'task_id' => $task->id,
                'action' => 'created',
                'previous_key' => null,
                'key' => $status->id,
                'created_by' => $admin->id,
            ]);
        }
    }

    private function seedFinancialTransactions(User $admin, $allUsers, $clients, $clientContracts, $wallets, $categories): void
    {
        $startMonth = now()->copy()->startOfYear();

        foreach (range(0, 11) as $monthIndex) {
            $referenceDate = $startMonth->copy()->addMonths($monthIndex)->day(10);
            $referencePeriod = $referenceDate->format('Y-m');

            foreach ($clientContracts as $contractIndex => $clientContract) {
                $client = $clients->firstWhere('id', $clientContract->client_id);

                FinancialTransaction::create([
                    'type' => 'entrada',
                    'origin_type' => 'recorrente',
                    'billing_status' => $monthIndex <= now()->month - 1 ? 'pago' : 'pendente',
                    'name' => 'Mensalidade ' . $client->name,
                    'wallet_id' => $wallets->get('Conta Corrente')->id,
                    'category_id' => $categories->get('Receita Recorrente de Contrato')->id,
                    'client_id' => $client->id,
                    'client_contract_id' => $clientContract->id,
                    'reference_period' => $referencePeriod,
                    'counterparty_type' => 'client',
                    'counterparty_id' => $client->id,
                    'date' => $referenceDate->toDateString(),
                    'due_date' => $referenceDate->toDateString(),
                    'paid_at' => $monthIndex <= now()->month - 1 ? $referenceDate->copy()->addDays(2) : null,
                    'amount' => $clientContract->amount,
                    'description' => 'Cobrança recorrente gerada pelo seeder.',
                    'status' => true,
                    'created_by' => $admin->id,
                ]);

                if ($contractIndex >= 2) {
                    continue;
                }

                $expenseCategory = $contractIndex === 0
                    ? $categories->get('Tráfego Pago')
                    : $categories->get('Ferramentas');

                FinancialTransaction::create([
                    'type' => 'debito',
                    'origin_type' => 'avulsa',
                    'billing_status' => $monthIndex <= now()->month - 1 ? 'pago' : 'pendente',
                    'name' => $contractIndex === 0 ? 'Investimento em mídia' : 'Licenças de software',
                    'wallet_id' => $wallets->get('Cartão Corporativo')->id,
                    'category_id' => $expenseCategory->id,
                    'counterparty_type' => 'user',
                    'counterparty_id' => $allUsers->get($contractIndex + 1)->id,
                    'date' => $referenceDate->copy()->addDays(5)->toDateString(),
                    'due_date' => $referenceDate->copy()->addDays(5)->toDateString(),
                    'paid_at' => $monthIndex <= now()->month - 1 ? $referenceDate->copy()->addDays(6) : null,
                    'amount' => $contractIndex === 0 ? 850 + ($monthIndex * 35) : 420 + ($monthIndex * 20),
                    'description' => 'Despesa operacional gerada pelo seeder.',
                    'status' => true,
                    'created_by' => $admin->id,
                ]);
            }
        }

        FinancialTransaction::create([
            'type' => 'entrada',
            'origin_type' => 'avulsa',
            'billing_status' => 'pago',
            'name' => 'Consultoria estratégica',
            'wallet_id' => $wallets->get('Reserva')->id,
            'category_id' => $categories->get('Consultoria')->id,
            'counterparty_type' => 'client',
            'counterparty_id' => $clients->first()->id,
            'client_id' => $clients->first()->id,
            'date' => now()->subDays(12)->toDateString(),
            'due_date' => now()->subDays(12)->toDateString(),
            'paid_at' => now()->subDays(10),
            'amount' => 6800,
            'description' => 'Receita avulsa para diversificar os gráficos.',
            'status' => true,
            'created_by' => $admin->id,
        ]);
    }

    private function seedSessions($allUsers): void
    {
        foreach ($allUsers as $index => $user) {
            DB::table('sessions')->insert([
                'id' => 'seed-session-' . $user->id,
                'user_id' => $user->id,
                'ip_address' => '127.0.0.' . ($index + 1),
                'user_agent' => 'Seeder Browser',
                'payload' => '',
                'last_activity' => now()->subMinutes($index * 17)->timestamp,
            ]);
        }
    }

    private function seedAgendas(User $admin, $allUsers, $clients): void
    {
        $agendaData = [
            ['name' => 'Reunião semanal de time', 'color' => '#0EA5E9', 'general' => true],
            ['name' => 'Call com clientes', 'color' => '#22C55E', 'general' => false],
            ['name' => 'Revisão financeira', 'color' => '#F59E0B', 'general' => true],
        ];

        foreach ($agendaData as $index => $agendaItem) {
            $agenda = Agenda::create([
                'name' => $agendaItem['name'],
                'description' => 'Agenda criada automaticamente no seeder para testes.',
                'general' => $agendaItem['general'],
                'date_start' => now()->addDays($index)->toDateString(),
                'date_end' => now()->addMonth()->toDateString(),
                'hour_start' => '09:00:00',
                'hour_end' => '10:00:00',
                'recurrent' => true,
                'frequency' => 'weekly',
                'week_days' => '1,3,5',
                'duration' => '01:00:00',
                'color' => $agendaItem['color'],
                'status' => true,
                'created_by' => $admin->id,
            ]);

            foreach ($allUsers->take(3) as $member) {
                AgendaMember::create([
                    'type' => 'user',
                    'member_id' => $member->id,
                    'agenda_id' => $agenda->id,
                    'status' => true,
                ]);
            }

            AgendaMember::create([
                'type' => 'client',
                'member_id' => $clients->get($index % $clients->count())->id,
                'agenda_id' => $agenda->id,
                'status' => true,
            ]);
        }
    }

    private function seedSidebarPreferences($allUsers, $projects): void
    {
        foreach ($allUsers as $user) {
            foreach ($projects as $project) {
                UserPreferrence::create([
                    'type' => 'sidebarProjectsOrder',
                    'value' => $project->id,
                    'created_by' => $user->id,
                ]);
            }
        }
    }
}
