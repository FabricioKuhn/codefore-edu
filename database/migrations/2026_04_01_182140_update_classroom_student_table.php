<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('classroom_student', function (Blueprint $table) {
        // Aulas totais do aluno naquela turma (flexível)
        $table->integer('student_total_lessons')->nullable(); 
        // Para sabermos qual é a "Aula 1" dele, usamos o created_at que já existe na pivô
    });
}
};
