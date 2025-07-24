<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agendas_member', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['user', 'client']);
            $table->foreignId('member_id');
            $table->foreignId('agenda_id')->constrained('agenda');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas_member');
    }
};
