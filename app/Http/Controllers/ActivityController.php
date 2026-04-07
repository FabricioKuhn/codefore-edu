<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function show(Activity $activity)
    {
        if ($activity->classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }
        
        $activity->load('questions.options');
        return view('activities.show', compact('activity'));
    }

    public function create(Request $request)
    {
        $classroom = Classroom::findOrFail($request->classroom_id);
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }
        return view('activities.create', compact('classroom'));
    }

    /**
     * Salva uma nova Avaliação (Tarefa ou Prova)
     */
    public function store(Request $request)
    {
        // 1. Regras de Validação V2
        $rules = [
            'classroom_id' => 'required|exists:classrooms,id',
            'type' => 'required|in:task,exam',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_xp' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'time_limit_minutes' => 'nullable|integer|min:1',
        ];

        // Se for PROVA, exige que as configurações de sorteio sejam preenchidas
        if ($request->type === 'exam') {
            $rules['exam_settings'] = 'required|array';
            $rules['exam_settings.multiple_choice'] = 'required|integer|min:0';
            $rules['exam_settings.descriptive'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        // Define status inicial como rascunho
        $validated['status'] = 'draft';

        // 2. Salva no banco de dados
        $activity = \App\Models\Activity::create($validated);

        // 3. Redireciona para o painel de controle (show) da atividade
        return redirect()->route(auth()->user()->role . '.activities.show', $activity)
                         ->with('success', 'Avaliação criada! Agora configure as questões.');
    }


    public function edit(\App\Models\Activity $activity)
{
    // Trava de segurança (Tenant)
    if ($activity->classroom->institution_id !== auth()->user()->institution_id) {
        abort(403, 'Acesso negado.');
    }

    $classroom = $activity->classroom;

    return view('activities.edit', compact('activity', 'classroom'));
}
    /**
     * Atualiza as configurações da Avaliação
     */
    public function update(Request $request, \App\Models\Activity $activity)
    {
        // Trava de segurança (Garante que só mexa em atividades da própria escola)
        if ($activity->classroom->institution_id !== auth()->user()->institution_id) {
            abort(403, 'Acesso Negado.');
        }

        // 1. Regras de Validação V2
        $rules = [
            'type' => 'required|in:task,exam',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_xp' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,active,in_progress,closed,canceled',
        ];

        if ($request->type === 'exam') {
            $rules['exam_settings'] = 'required|array';
            $rules['exam_settings.multiple_choice'] = 'required|integer|min:0';
            $rules['exam_settings.descriptive'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        // Limpeza de dados: Se o prof mudou de Prova para Tarefa, apagamos o histórico de sorteio
        if ($request->type === 'task') {
            $validated['exam_settings'] = null;
        }

        // 2. Atualiza no banco
        $activity->update($validated);

        return redirect()->route(auth()->user()->role . '.activities.show', $activity)
                         ->with('success', 'Configurações da avaliação atualizadas com sucesso!');
    }


    /**
     * Vincula questões selecionadas do Banco à Avaliação
     */
    public function attachQuestions(Request $request, \App\Models\Activity $activity)
    {
        // Trava de segurança
        if ($activity->classroom->institution_id !== auth()->user()->institution_id) {
            abort(403, 'Acesso Negado.');
        }

        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);

        // O 'syncWithoutDetaching' adiciona as novas questões sem apagar as que já estavam lá
        $activity->questions()->syncWithoutDetaching($request->question_ids);

        return back()->with('success', count($request->question_ids) . ' questão(ões) vinculada(s) com sucesso!');
    }

    /**
     * Remove uma questão específica desta Avaliação
     */
    public function detachQuestion(\App\Models\Activity $activity, \App\Models\Question $question)
    {
        if ($activity->classroom->institution_id !== auth()->user()->institution_id) {
            abort(403, 'Acesso Negado.');
        }

        $activity->questions()->detach($question->id);

        return back()->with('success', 'Questão removida desta avaliação.');
    }

    /**
     * Atualiza o peso de uma questão APENAS para esta avaliação
     */
    public function updateQuestionWeight(Request $request, \App\Models\Activity $activity, \App\Models\Question $question)
    {
        if ($activity->classroom->institution_id !== auth()->user()->institution_id) {
            abort(403, 'Acesso Negado.');
        }

        $request->validate([
            'weight' => 'required|integer|min:1'
        ]);

        // Atualiza a coluna 'weight_override' na tabela Pivot (activity_question)
        $activity->questions()->updateExistingPivot($question->id, [
            'weight_override' => $request->weight
        ]);

        return back()->with('success', 'Peso da questão atualizado para esta avaliação!');
    }

    /**
     * Habilita ou Desabilita um aluno para fazer esta Avaliação
     */
    public function toggleStudent(\App\Models\Activity $activity, \App\Models\User $student)
    {
        if ($activity->classroom->institution_id !== auth()->user()->institution_id) {
            abort(403, 'Acesso Negado.');
        }

        // Procura se o aluno já tem um registro de controle (submission) criado. 
        // Se não tiver (porque a prova é nova), o Laravel cria um na hora.
        $submission = \App\Models\Submission::firstOrCreate(
            [
                'activity_id' => $activity->id,
                'student_id' => $student->id
            ],
            [
                'status' => 'pending',
                'is_enabled' => true // O padrão ao criar é true
            ]
        );

        

        // Inverte o status atual (Se tava true, vira false. Se tava false, vira true)
        $submission->update([
            'is_enabled' => !$submission->is_enabled
        ]);

        $statusMsg = $submission->is_enabled ? 'habilitado' : 'desabilitado (oculto)';

        return back()->with('success', "Aluno {$student->name} foi {$statusMsg} para esta avaliação.");
    }

    public function updateStudentDeadline(Request $request, \App\Models\Activity $activity, \App\Models\User $student)
{
    $request->validate([
        'custom_deadline' => 'nullable|date|after:now'
    ]);

    $submission = \App\Models\Submission::firstOrCreate(
        ['activity_id' => $activity->id, 'student_id' => $student->id],
        ['status' => 'pending', 'is_enabled' => true]
    );

    $submission->update([
        'custom_deadline' => $request->custom_deadline
    ]);

    return back()->with('success', "Prazo individual de {$student->name} atualizado!");
}


}
