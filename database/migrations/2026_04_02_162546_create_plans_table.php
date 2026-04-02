<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('plans', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        
        // Valores
        $table->decimal('monthly_price', 10, 2)->default(0);
        $table->decimal('annual_price', 10, 2)->nullable();
        
        // Limites (Deixando nullable para ser 'Ilimitado' quando o Admin não preencher)
        $table->integer('teacher_limit')->nullable();
        $table->integer('classroom_limit')->nullable();
        $table->integer('student_limit')->nullable();
        $table->integer('task_limit')->nullable(); // Limite de tarefas simultâneas
        
        // Status para inativar/ativar que você mapeou
        $table->boolean('is_active')->default(true);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
