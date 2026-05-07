<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'contract_id')) {
                $table->dropConstrainedForeignId('contract_id');
            }

            if (Schema::hasColumn('clients', 'contract_value')) {
                $table->dropColumn('contract_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'contract_id')) {
                $table->foreignId('contract_id')->nullable()->constrained('contracts')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('clients', 'contract_value')) {
                $table->string('contract_value')->nullable()->after('contract_id');
            }
        });
    }
};
