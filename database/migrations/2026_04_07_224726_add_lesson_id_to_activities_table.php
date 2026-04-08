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
        // nullOnDelete é a REGRA DE OURO do módulo. Se a aula for apagada, a atividade não é destruída, ela só fica "solta" na turma.
        $table->foreignId('lesson_id')->nullable()->after('id')->constrained()->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('activities', function (Blueprint $table) {
        $table->dropForeign(['lesson_id']);
        $table->dropColumn('lesson_id');
    });
}
};
