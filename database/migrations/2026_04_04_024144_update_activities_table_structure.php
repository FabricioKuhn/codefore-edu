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
    Schema::table('activities', function (Blueprint $table) {
        // 1. Adicionamos os campos que REALMENTE não existem ainda
        if (!Schema::hasColumn('activities', 'start_date')) {
            $table->timestamp('start_date')->nullable();
        }
        if (!Schema::hasColumn('activities', 'end_date')) {
            $table->timestamp('end_date')->nullable();
        }
        if (!Schema::hasColumn('activities', 'duration_minutes')) {
            $table->integer('duration_minutes')->nullable();
        }
        if (!Schema::hasColumn('activities', 'shuffle_options')) {
            $table->boolean('shuffle_options')->default(false);
        }

        // 2. O PULO DO GATO: Como o 'status' já existe, vamos ALTERAR (change) ele
        // Isso vai atualizar o ENUM com as novas opções que você pediu
        $table->enum('status', ['draft', 'active', 'in_progress', 'closed', 'canceled'])
              ->default('draft')
              ->change(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
