<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function createDashboardUser(string $role): User
{
    DB::table('users')->insert([
        'id' => 1,
        'name' => 'Usuário Teste',
        'role' => $role,
        'position_id' => 1,
        'email' => 'usuario-dashboard@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'sidebar' => true,
        'sounds' => true,
        'status' => true,
        'created_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::find(1);
}

test('dashboard pessoal continua na rota raiz', function () {
    $user = createDashboardUser('Administrador');

    $response = $this->actingAs($user)->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertSee('Minhas tarefas');
    $response->assertDontSee('Finanças do ano');
});

test('administrador acessa dashboard administrativa', function () {
    $user = createDashboardUser('Administrador');

    $response = $this->actingAs($user)->get(route('dashboard.admin'));

    $response->assertOk();
    $response->assertSee('Dashboard Admin');
    $response->assertSee('Tipos de contrato');
    $response->assertSee('Finanças do ano');
});

test('usuario nao acessa dashboard administrativa', function () {
    $user = createDashboardUser('Usuário');

    $response = $this->actingAs($user)->get(route('dashboard.admin'));

    $response->assertForbidden();
});
