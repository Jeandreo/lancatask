<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_categories', function (Blueprint $table) {
            $table->enum('type', ['entrada', 'debito'])->default('debito')->after('name');
        });

        DB::table('financial_categories')->whereNull('type')->update(['type' => 'debito']);
    }

    public function down(): void
    {
        Schema::table('financial_categories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
