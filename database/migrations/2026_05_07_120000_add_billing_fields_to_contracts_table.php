<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedInteger('period_in_months')->default(1)->after('name');
            $table->unsignedInteger('duration_in_months')->default(12)->after('period_in_months');
            $table->foreignId('wallet_id')->nullable()->after('duration_in_months')->constrained('financial_wallets')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('wallet_id')->constrained('financial_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('wallet_id');
            $table->dropColumn('duration_in_months');
            $table->dropColumn('period_in_months');
        });
    }
};
