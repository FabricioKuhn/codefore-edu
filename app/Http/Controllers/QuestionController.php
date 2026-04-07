<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    /**
     * Lista as questões do Banco da Instituição
     */
    public function index()
    {
        $questions = Question::where('institution_id', auth()->user()->institution_id)
            ->withCount('options') // Conta quantas alternativas tem (útil para ME)
            ->latest()
            ->paginate(15);

        return view('questions.index', compact('questions'));
    }

    /**
     * Tela de criação de nova questão
     */
    public function create()
    {
        return view('questions.create');
    }

    /**
     * Salva a questão e suas alternativas (se for múltipla escolha)
     */
    public function store(Request $request)
{
    // 1. Validação (Incluindo os novos campos)
    $rules = [
        'type' => 'required|in:multiple_choice,descriptive',
        'statement' => 'required|string',
        'default_weight' => 'required|integer|min:1',
        'expected_answer' => 'nullable|string',
        'guidelines' => 'nullable|string', // Dicas/Orientações
        'external_link' => 'nullable|url',
        'external_link_label' => 'nullable|string|max:50',
        'attachments' => 'nullable|array',
        'attachments.*' => 'image|max:2048', // Cada imagem até 2MB
        'tags' => 'nullable|array',
        'tags.*' => 'string|max:50',
    ];

    if ($request->type === 'multiple_choice') {
        $rules['options'] = 'required|array|min:2';
        $rules['options.*.content'] = 'required|string';
        $rules['correct_option'] = 'required|integer';
    }

    $request->validate($rules);

    // 2. Processamento de Imagens (Antes da Transaction)
    $attachmentPaths = [];
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            // Salva na pasta 'questions' dentro de 'public'
            $attachmentPaths[] = $file->store('questions', 'public');
        }
    }

    // 3. Salva no banco
    \Illuminate\Support\Facades\DB::transaction(function () use ($request, $attachmentPaths) {
        
        $question = \App\Models\Question::create([
            'institution_id' => auth()->user()->institution_id,
            'user_id' => auth()->id(), // Importante saber quem criou
            'type' => $request->type,
            'statement' => $request->statement,
            'guidelines' => $request->guidelines,
            'external_link' => $request->external_link,
            'external_link_label' => $request->external_link_label,
            'attachments' => $attachmentPaths, // Salva o array de caminhos
            'expected_answer' => $request->type === 'descriptive' ? $request->expected_answer : null,
            'default_weight' => $request->default_weight,
            'status' => true,
            'tags' => $request->tags ?? [],
        ]);

        if ($request->type === 'multiple_choice' && $request->has('options')) {
            foreach ($request->options as $index => $optionData) {
                \App\Models\QuestionOption::create([
                    'question_id' => $question->id,
                    'content' => $optionData['content'],
                    'is_correct' => ($index == $request->correct_option), 
                ]);
            }
        }
    });

    return redirect()->route(auth()->user()->role . '.questions.index')
                     ->with('success', 'Questão adicionada ao Banco com sucesso!');
}

            /**
     * Tela de Edição
     */
    public function edit(Question $question)
    {
        // Trava de segurança (Tenant)
        if ($question->institution_id !== auth()->user()->institution_id) {
            abort(403, 'Acesso negado.');
        }

        // Carrega as opções (se houver) para o Alpine.js ler
        $question->load('options');

        return view('questions.edit', compact('question'));
    }

    /**
     * Salva as alterações da Questão
     */
    public function update(Request $request, Question $question)
{
    if ($question->institution_id !== auth()->user()->institution_id) {
        abort(403, 'Acesso negado.');
    }

    // 1. Regras de Validação (Incluindo os novos campos)
    $rules = [
        'type' => 'required|in:multiple_choice,descriptive',
        'statement' => 'required|string',
        'default_weight' => 'required|integer|min:1',
        'expected_answer' => 'nullable|string',
        'guidelines' => 'nullable|string', // Dicas/Orientações
        'external_link' => 'nullable|url',
        'external_link_label' => 'nullable|string|max:50',
        'attachments' => 'nullable|array',
        'attachments.*' => 'image|max:2048', // Cada imagem até 2MB
        'tags' => 'nullable|array',
        'tags.*' => 'string|max:50',
    ];

    if ($request->type === 'multiple_choice') {
        $rules['options'] = 'required|array|min:2';
        $rules['options.*.content'] = 'required|string';
        $rules['correct_option'] = 'required|integer';
    }

    $request->validate($rules);

    // 2. Lógica de Imagens (Mantendo as antigas e somando as novas)
    $attachmentPaths = $question->attachments ?? []; // Pega o que já existe no banco
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $attachmentPaths[] = $file->store('questions', 'public');
        }
    }

    // 3. Salvamento em Transação
    \Illuminate\Support\Facades\DB::transaction(function () use ($request, $question, $attachmentPaths) {
        
        // Atualiza a base da questão com os novos campos
        $question->update([
            'type' => $request->type,
            'statement' => $request->statement,
            'guidelines' => $request->guidelines,
            'external_link' => $request->external_link,
            'external_link_label' => $request->external_link_label,
            'attachments' => $attachmentPaths, // Salva o array atualizado
            'expected_answer' => $request->type === 'descriptive' ? $request->expected_answer : null,
            'default_weight' => $request->default_weight,
            'tags' => $request->tags ?? [],
        ]);

        // Atualiza as alternativas
        if ($request->type === 'multiple_choice' && $request->has('options')) {
            $question->options()->delete();
            
            foreach ($request->options as $index => $optionData) {
                \App\Models\QuestionOption::create([
                    'question_id' => $question->id,
                    'content' => $optionData['content'],
                    'is_correct' => ($index == $request->correct_option),
                ]);
            }
        } elseif ($request->type === 'descriptive') {
            $question->options()->delete();
        }
    });

    return redirect()->route(auth()->user()->role . '.questions.index')
                     ->with('success', 'Questão atualizada com sucesso!');
}

    /**
     * Ativa ou Inativa a questão no banco
     */
    public function updateStatus(\App\Models\Question $question)
    {
        // Trava de segurança
        if ($question->institution_id !== auth()->user()->institution_id) {
            abort(403, 'Acesso negado.');
        }

        // Inverte o status atual
        $question->update([
            'status' => !$question->status
        ]);

        $mensagem = $question->status ? 'Questão ativada com sucesso!' : 'Questão inativada com sucesso!';

        return back()->with('success', $mensagem);
    }
}