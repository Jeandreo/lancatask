<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('contracts')->update([
            'is_open_ended' => true,
            'duration_in_months' => null,
            'wallet_id' => 1,
            'category_id' => 1,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('contracts')->where('wallet_id', 1)->where('category_id', 1)->update([
            'is_open_ended' => false,
            'updated_at' => now(),
        ]);
    }
};
