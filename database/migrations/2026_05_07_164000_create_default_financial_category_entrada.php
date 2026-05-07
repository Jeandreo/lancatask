<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categoryExists = DB::table('financial_categories')
            ->where('name', 'Categoria Padrão')
            ->where('type', 'entrada')
            ->exists();

        if ($categoryExists) {
            return;
        }

        $firstUser = DB::table('users')->orderBy('id')->first();

        if (!$firstUser) {
            return;
        }

        DB::table('financial_categories')->insert([
            'name' => 'Categoria Padrão',
            'type' => 'entrada',
            'status' => true,
            'created_by' => $firstUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('financial_categories')
            ->where('name', 'Categoria Padrão')
            ->where('type', 'entrada')
            ->delete();
    }
};
