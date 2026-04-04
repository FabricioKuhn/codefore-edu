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
    Schema::table('attendances', function (Blueprint $table) {
        // 1. Adicionamos a coluna de justificativa que está faltando no seu print
        if (!Schema::hasColumn('attendances', 'justification')) {
            $table->text('justification')->after('status')->nullable();
        }

        // 2. Atualizamos o ENUM de status para incluir 'justified' (justificada)
        // O Laravel 12 trata isso de forma nativa com o change()
        $table->enum('status', ['present', 'absent', 'justified', 'not_started'])
              ->default('not_started')
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
