<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->enum('origin_type', ['recorrente', 'avulsa'])->default('avulsa')->after('type');
            $table->enum('billing_status', ['pendente', 'pago', 'vencido', 'cancelado'])->default('pendente')->after('origin_type');
            $table->date('due_date')->nullable()->after('date');
            $table->timestamp('paid_at')->nullable()->after('due_date');
            $table->foreignId('client_contract_id')->nullable()->after('client_id')->constrained('client_contracts')->nullOnDelete();
            $table->string('reference_period', 7)->nullable()->after('client_contract_id');

            $table->index(['client_contract_id', 'reference_period']);
            $table->unique(['client_contract_id', 'reference_period'], 'uniq_client_contract_reference_period');
        });
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropUnique('uniq_client_contract_reference_period');
            $table->dropIndex(['client_contract_id', 'reference_period']);
            $table->dropConstrainedForeignId('client_contract_id');
            $table->dropColumn('reference_period');
            $table->dropColumn('paid_at');
            $table->dropColumn('due_date');
            $table->dropColumn('billing_status');
            $table->dropColumn('origin_type');
        });
    }
};
