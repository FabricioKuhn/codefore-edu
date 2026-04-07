<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela do Banco de Questões Global (Pertence à Instituição)
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete(); 
            $table->string('type'); // 'multiple_choice' ou 'descriptive'
            $table->text('statement'); // Enunciado
            $table->text('guidelines')->nullable(); // Dicas de correção
            $table->json('attachments')->nullable(); // Imagens/Links
            $table->text('expected_answer')->nullable(); // Gabarito para o professor
            $table->integer('default_weight')->default(1);
            $table->boolean('status')->default(true); // Ativo/Inativo no banco
            $table->timestamps();
        });

        // 2. Opções das Questões de Múltipla Escolha
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // 3. Tabela de Avaliações (Tarefas e Provas)
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            
            $table->string('type')->default('task'); // 'task' ou 'exam'
            $table->string('title');
            $table->text('description')->nullable();
            
            // Datas e Cronômetro
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('time_limit_minutes')->nullable();
            
            // XP e Moedas
            $table->integer('base_xp');
            $table->decimal('coin_conversion_rate', 5, 2)->default(0.10);
            
            // Exclusivo para Provas (Sorteio)
            $table->json('exam_settings')->nullable(); // Ex: {"multiple_choice": 5, "descriptive": 5}
            
            $table->enum('status', ['draft', 'active', 'in_progress', 'closed', 'canceled'])->default('draft');
            $table->timestamps();
        });

        // 4. Tabela Pivot (Liga Tarefa/Prova às Questões Selecionadas)
        Schema::create('activity_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->integer('weight_override')->nullable(); // Caso o prof queira mudar o peso só nessa prova
            $table->timestamps();
        });

        // 5. Tabela de Controle do Aluno (Workflow e Cronômetro)
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            
            // Controle Individual
            $table->boolean('is_enabled')->default(true); 
            $table->dateTime('custom_deadline')->nullable(); 
            $table->text('deadline_justification')->nullable(); 
            
            // Status do Aluno e Tempos
            $table->enum('status', ['pending', 'in_progress', 'waiting_evaluation', 'evaluated'])->default('pending');
            $table->timestamp('started_at')->nullable(); // Trava o início do cronômetro
            $table->timestamp('submitted_at')->nullable(); // Hora real de entrega
            
            // Recompensas
            $table->integer('earned_xp')->nullable();
            $table->integer('earned_coins')->nullable();
            
            $table->timestamps();
            
            // O aluno só pode ter 1 controle por atividade
            $table->unique(['activity_id', 'student_id']);
        });

        // 6. Respostas do Aluno
        Schema::create('submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->text('text_answer')->nullable();
            
            $table->decimal('ai_suggested_score', 5, 2)->nullable();
            $table->text('ai_suggested_feedback')->nullable();
            
            $table->decimal('final_score', 5, 2)->nullable(); // Nota dada pelo professor
            $table->text('teacher_feedback')->nullable(); // Devolutiva
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_answers');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('activity_question');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
    }
};