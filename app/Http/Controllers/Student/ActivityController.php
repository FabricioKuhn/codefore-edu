<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Submission;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Tela de Instruções (Check-in)
     */
    public function show(Activity $activity)
{
    // Verifica se o aluno pertence à turma
    if (!$activity->classroom->students->contains(auth()->id())) {
        abort(403);
    }

    $submission = $activity->submissions()->where('student_id', auth()->id())->first();

    // 🌟 A MÁGICA: Se já foi avaliado, manda para a tela de resultado/gabarito
    if ($submission && in_array($submission->status, ['evaluated', 'completed'])) {
        $questions = ($activity->type === 'exam') 
            ? $submission->questions()->with('options')->get() 
            : $activity->questions()->with('options')->get();

        return view('student.activities.result', compact('activity', 'submission', 'questions'));
    }

    return view('student.activities.show', compact('activity', 'submission'));
}

    /**
     * Inicia a missão (Marca tempo e sorteia questões se for Prova)
     */
    public function start(Activity $activity)
    {
        $studentId = auth()->id();

        $submission = Submission::firstOrCreate(
            ['activity_id' => $activity->id, 'student_id' => $studentId],
            ['status' => 'pending', 'is_enabled' => true]
        );

        if (!$submission->started_at) {
            $submission->update([
                'started_at' => now(),
                'status' => 'in_progress'
            ]);

            // Lógica do "Crupiê": Sorteio de questões para Provas (Exam)
            if ($activity->type === 'exam') {
                $this->generateDynamicExam($activity, $submission);
            }
        }

        return redirect()->route('student.activities.play', $activity);
    }

    /**
     * A Arena de Respostas
     */
    public function play(Activity $activity)
    {
        $submission = $activity->submissions()->where('student_id', auth()->id())->firstOrFail();

        // Bloqueia se já foi enviado
        if (in_array($submission->status, ['waiting_evaluation', 'evaluated'])) {
            return redirect()->route('student.classrooms.show', $activity->classroom_id)
                             ->with('error', 'Esta missão já foi concluída.');
        }

        // Se for TAREFA normal, carrega todas as questões
        if ($activity->type !== 'exam') {
            $questions = $activity->questions;
        } 
        // Se for PROVA, tenta puxar do relacionamento.
        else {
            $questions = $submission->questions;

            // 🛡️ PLANO B: Se o relacionamento falhou ou está vazio, calcula dinamicamente com Seed
            if ($questions === null || $questions->isEmpty()) {
                $settings = $activity->exam_settings ?? [];
                $mcCount = (int) ($settings['multiple_choice'] ?? 0);
                $descCount = (int) ($settings['descriptive'] ?? 0);

                // Usa a combinação do ID do aluno + ID da atividade como "semente" matemática.
                // Isso garante que o random() dê sempre o MESMO resultado para este aluno nesta prova.
                $seed = auth()->id() + $activity->id;
                mt_srand($seed); 

                $allMc = $activity->questions->where('type', 'multiple_choice')->values();
                $allDesc = $activity->questions->where('type', 'descriptive')->values();

                $mcQuestions = $allMc->random(min($mcCount, $allMc->count()));
                $descQuestions = $allDesc->random(min($descCount, $allDesc->count()));

                $questions = collect($mcQuestions)->merge($descQuestions)->shuffle();
                
                // Limpa o gerador aleatório para não afetar o resto do sistema
                mt_srand();
            }
        }

        return view('student.activities.play', compact('activity', 'submission', 'questions'));
    }

    /**
     * Finaliza a missão e envia respostas
     */
    public function submit(Request $request, Activity $activity)
    {
        $submission = $activity->submissions()->where('student_id', auth()->id())->firstOrFail();

        $submission->update([
            'answers' => $request->answers,
            'finished_at' => now(),
            'status' => 'waiting_evaluation'
        ]);

        return redirect()->route('student.classrooms.show', $activity->classroom_id)
                         ->with('success', 'Missão cumprida! Suas respostas foram enviadas.');
    }

    /**
     * Lógica privada para sortear questões do banco
     */
    private function generateDynamicExam($activity, $submission)
    {
        if ($submission->questions()->exists()) return;

        $settings = $activity->exam_settings;
        $questionsToAttach = collect();

        foreach (['multiple_choice', 'descriptive'] as $type) {
            $count = $settings[$type] ?? 0;
            if ($count > 0) {
                $randomIds = $activity->questions()
                    ->where('type', $type)
                    ->inRandomOrder()
                    ->limit($count)
                    ->pluck('questions.id');
                
                $questionsToAttach = $questionsToAttach->merge($randomIds);
            }
        }

        $submission->questions()->sync($questionsToAttach);
    }
}