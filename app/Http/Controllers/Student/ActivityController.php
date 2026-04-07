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
        // Verifica se o aluno pertence à turma da atividade
        if (!$activity->classroom->students->contains(auth()->id())) {
            abort(403, 'Acesso negado.');
        }

        $submission = $activity->submissions()->where('student_id', auth()->id())->first();

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

        // Se for PROVA, pega as questões sorteadas. Se for TAREFA, pega todas.
        $questions = ($activity->type === 'exam') 
            ? $submission->questions 
            : $activity->questions;

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