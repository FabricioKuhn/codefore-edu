<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function store(Request $request, Activity $activity)
    {
        if ($activity->classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }

        $validated = $this->validateQuestion($request);

        $attachments = $this->processAttachments($request);

        $question = $activity->questions()->create([
            'type' => $validated['type'],
            'statement' => $validated['statement'],
            'weight' => $validated['weight'],
            'expected_answer' => $validated['type'] === 'descriptive' ? $validated['expected_answer'] : null,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        $this->syncOptions($question, $validated);

        return redirect()->route('activities.show', $activity)
                         ->with('success', 'Questão criada com sucesso!');
    }

    public function update(Request $request, Question $question)
    {
        if ($question->activity->classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }

        $validated = $this->validateQuestion($request);

        // Obter anexos antigos para não perder caso não envie novos (simplificado)
        $attachments = $question->attachments ?? [];
        
        // Se novos anexos de imagens subirem, adiciona na lista
        $newAttachments = $this->processAttachments($request);
        if (!empty($newAttachments)) {
            $attachments = array_merge($attachments, $newAttachments);
        }

        $question->update([
            'type' => $validated['type'],
            'statement' => $validated['statement'],
            'weight' => $validated['weight'],
            'expected_answer' => $validated['type'] === 'descriptive' ? $validated['expected_answer'] : null,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        // Se era multipla escolha e mudou pra descritiva, deletar antigas.
        $question->options()->delete();
        $this->syncOptions($question, $validated);

        return redirect()->route('activities.show', $question->activity_id)
                         ->with('success', 'Questão atualizada com sucesso!');
    }

    private function validateQuestion(Request $request)
    {
        $rules = [
            'type' => 'required|in:multiple_choice,descriptive',
            'statement' => 'required|string',
            'weight' => 'required|integer|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'button_text' => 'nullable|string',
            'button_url' => 'nullable|url',
        ];

        if ($request->type === 'multiple_choice') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*'] = 'required|string';
            $rules['correct_option'] = 'required|integer|min:0';
        } elseif ($request->type === 'descriptive') {
            $rules['expected_answer'] = 'required|string';
        }

        return $request->validate($rules);
    }

    private function processAttachments(Request $request)
    {
        $attachments = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('questions', 'public');
                $attachments[] = [
                    'type' => 'image',
                    'url'  => Storage::url($path),
                ];
            }
        }

        if ($request->filled('button_text') && $request->filled('button_url')) {
            $attachments[] = [
                'type' => 'link_button',
                'text' => $request->button_text,
                'url'  => $request->button_url,
            ];
        }

        return $attachments;
    }

    private function syncOptions(Question $question, array $validated)
    {
        if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
            foreach ($validated['options'] as $index => $optionContent) {
                $question->options()->create([
                    'content' => $optionContent,
                    'is_correct' => (string)$index === (string)$validated['correct_option'],
                ]);
            }
        }
    }
}
