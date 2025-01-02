<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->date('date_end')->nullable()->after('date');
            $table->date('date_start')->nullable()->after('date');
        });

        // Preenche os campos date_start e date_end com os valores de date
        DB::table('tasks')->get()->each(function ($task) {
            DB::table('tasks')->where('id', $task->id)->update([
                'date_start' => $task->date,
                'date_end' => $task->date,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['date_end', 'date_start']);
        });
    }
};
