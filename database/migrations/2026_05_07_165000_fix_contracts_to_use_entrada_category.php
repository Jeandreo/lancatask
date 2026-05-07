<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $entradaCategory = DB::table('financial_categories')
            ->where('name', 'Categoria Padrão')
            ->where('type', 'entrada')
            ->orderBy('id')
            ->first();

        if (!$entradaCategory) {
            $entradaCategory = DB::table('financial_categories')
                ->where('type', 'entrada')
                ->orderBy('id')
                ->first();
        }

        if (!$entradaCategory) {
            return;
        }

        DB::table('contracts')->update([
            'category_id' => $entradaCategory->id,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Ajuste corretivo sem rollback destrutivo.
    }
};
