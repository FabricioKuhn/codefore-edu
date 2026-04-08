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
    Schema::create('lesson_student', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // O Aluno
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
        
        // Garante que o aluno não conclua a mesma aula duas vezes
        $table->unique(['lesson_id', 'user_id']); 
    });
}

public function down(): void
{
    Schema::dropIfExists('lesson_student');
}
};
