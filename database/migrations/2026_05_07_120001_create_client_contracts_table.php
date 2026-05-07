<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('period_in_months');
            $table->unsignedInteger('duration_in_months');
            $table->boolean('status')->default(true);
            $table->foreignId('filed_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_contracts');
    }
};
