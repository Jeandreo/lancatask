<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaultWallet = DB::table('financial_wallets')->where('id', 1)->first();

        if ($defaultWallet) {
            DB::table('contracts')
                ->whereNull('wallet_id')
                ->update([
                    'wallet_id' => 1,
                    'updated_at' => now(),
                ]);
        }

        $defaultEntradaCategory = DB::table('financial_categories')
            ->where('id', 1)
            ->where('type', 'entrada')
            ->first();

        if ($defaultEntradaCategory) {
            DB::table('contracts')
                ->whereNull('category_id')
                ->update([
                    'category_id' => 1,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Migration corretiva sem rollback destrutivo.
    }
};
