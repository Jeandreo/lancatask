<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->boolean('is_open_ended')->default(false)->after('duration_in_months');
        });

        Schema::table('client_contracts', function (Blueprint $table) {
            $table->unsignedInteger('duration_in_months')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('client_contracts', function (Blueprint $table) {
            $table->unsignedInteger('duration_in_months')->nullable(false)->change();
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('is_open_ended');
        });
    }
};
