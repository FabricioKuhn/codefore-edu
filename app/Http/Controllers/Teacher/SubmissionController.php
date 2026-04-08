<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * Tela 1: Lista todos os alunos que enviaram a atividade
     */
    public function index(Activity $activity)
    {
        // 🛡️ Segurança: Garante que o professor é o dono da turma
        if ($activity->classroom->teacher_id !== auth()->id()) {
            abort(403, 'Acesso Negado. Esta turma não é sua.');
        }

        // Busca todas as submissões dessa atividade junto com os dados dos alunos
        // Ordena para que os "Aguardando Correção" fiquem no topo!
        $submissions = $activity->submissions()
            ->with('student')
            ->orderByRaw("FIELD(status, 'waiting_evaluation') DESC")
            ->orderBy('submitted_at', 'asc') 
            ->get();

        return view('teachers.submissions.index', compact('activity', 'submissions'));
    }

    /**
     * Tela 2: A tela de correção individual (Vamos fazer no próximo passo)
     */
    public function show(Activity $activity, Submission $submission)
{
    if ($activity->classroom->teacher_id !== auth()->id()) abort(403);

    // Carregamos as questões com as opções e garantimos que os dados da pivot (pesos) venham junto
    $questions = ($activity->type === 'exam') 
        ? $submission->questions()->with('options')->get() 
        : $activity->questions()->with('options')->get();

    return view('teachers.submissions.show', compact('activity', 'submission', 'questions'));
}

    /**
     * Ação: Salva a nota/XP e o feedback do professor
     */
    public function evaluate(Request $request, Activity $activity, Submission $submission)
{
    if ($activity->classroom->teacher_id !== auth()->id()) abort(403);

    // Recebe as notas individuais e o feedback
    // $request->scores é um array: [question_id => percentage_0_to_100]
    $scores = $request->input('scores', []);
    $feedbacks = $request->input('question_feedbacks', []);
    
    $totalWeight = 0;
    $earnedPoints = 0;

    $questions = ($activity->type === 'exam') ? $submission->questions : $activity->questions;

    foreach ($questions as $question) {
        // Pega o peso (usando o override da pivot se existir, senão o peso padrão da questão)
        $weight = $question->pivot->weight_override ?? $question->weight ?? 1;
        $totalWeight += $weight;

        if ($question->type === 'multiple_choice') {
            // Avaliação Automática: acertou tudo ou nada
            $studentAnswer = $submission->answers[$question->id] ?? null;
            $correctOptionKey = $question->options->where('is_correct', true)->keys()->first();
            
            if ($studentAnswer !== null && (string)$studentAnswer === (string)$correctOptionKey) {
                $earnedPoints += $weight;
            }
        } else {
            // Avaliação Manual (Descritiva): peso * (porcentagem / 100)
            $percentage = $scores[$question->id] ?? 0;
            $earnedPoints += $weight * ($percentage / 100);
        }
    }

    // Cálculo final da porcentagem de acerto da prova
    $finalPercentage = $totalWeight > 0 ? ($earnedPoints / $totalWeight) : 0;
    $finalXp = round($activity->base_xp * $finalPercentage);

    $submission->update([
    'status' => 'evaluated',
    'earned_xp' => $request->input('earned_xp'),
    'feedback' => $request->input('feedback'), // Feedback geral
    'teacher_notes' => [
        'scores' => $request->input('scores'), // Notas % por questão
        'question_feedbacks' => $request->input('question_feedbacks') // Feedbacks por questão
    ],
    'evaluated_at' => now(),
]);
    // Incrementar XP do aluno (Se o seu sistema já tiver essa coluna no User)
    // $submission->student->increment('total_xp', $finalXp);

    return redirect()->route('teacher.submissions.index', $activity)
                     ->with('success', "Avaliação concluída! {$submission->student->name} recebeu {$finalXp} XP.");
}
}