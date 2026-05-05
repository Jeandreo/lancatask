<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['entrada', 'debito']);
            $table->string('name');
            $table->foreignId('wallet_id')->constrained('financial_wallets');
            $table->foreignId('category_id')->constrained('financial_categories');
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->enum('counterparty_type', ['client', 'user'])->nullable();
            $table->unsignedBigInteger('counterparty_id')->nullable();
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('filed_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('date');
            $table->index('wallet_id');
            $table->index('category_id');
            $table->index('type');
            $table->index(['counterparty_type', 'counterparty_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
