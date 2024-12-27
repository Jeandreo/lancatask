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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules');
            $table->foreignId('task_id')->nullable()->constrained('tasks');
            $table->foreignId('status_id')->default(1)->constrained('statuses');
            $table->boolean('checked')->default(false);
            $table->dateTime('checked_at')->nullable();
            $table->integer('order')->default(0);
            $table->integer('priority')->default(0);
            $table->dateTime('date')->nullable();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->integer('status')->default(1);
            $table->foreignId('filed_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
