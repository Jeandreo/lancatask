<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $walletExists = DB::table('financial_wallets')
            ->where('name', 'Carteira Padrão')
            ->exists();

        if ($walletExists) {
            return;
        }

        $firstUser = DB::table('users')->orderBy('id')->first();

        if (!$firstUser) {
            return;
        }

        DB::table('financial_wallets')->insert([
            'name' => 'Carteira Padrão',
            'status' => true,
            'created_by' => $firstUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('financial_wallets')
            ->where('name', 'Carteira Padrão')
            ->delete();
    }
};
