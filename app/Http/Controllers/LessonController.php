<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function storeAttendance(Request $request, \App\Models\Lesson $lesson)
    {
        $user = auth()->user();

    // 🛡️ CHAVE-MESTRA: 
    // Só bloqueia se o usuário for um PROFESSOR e a aula não for dele.
    // Se for ADMIN, ele pula esse 'if' e executa a ação.
    if ($user->role === 'teacher' && $lesson->classroom->teacher_id !== $user->id) {
        abort(403, 'Acesso negado. Esta aula pertence a outro professor.');
    }
        
        

        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,justified',
        ]);

        if ($lesson->status === 'canceled') {
        return back()->with('error', 'Não é possível realizar ações em uma aula cancelada.');
    }

        foreach ($validated['attendance'] as $studentId => $data) {
            $lesson->attendances()->updateOrCreate(
                ['user_id' => $studentId],
                [
                    'status' => $data['status'],
                    'justification' => $data['justification'] ?? null
                ]
            );
        }

        // Fecha a aula ao realizar a chamada
        $lesson->update(['status' => 'recorded']);

        return back()->with('success', 'Chamada realizada com sucesso!');
    }

    public function cancel(Request $request, Lesson $lesson)
    {
        $user = auth()->user();

    // 🛡️ CHAVE-MESTRA: 
    // Só bloqueia se o usuário for um PROFESSOR e a aula não for dele.
    // Se for ADMIN, ele pula esse 'if' e executa a ação.
    if ($user->role === 'teacher' && $lesson->classroom->teacher_id !== $user->id) {
        abort(403, 'Acesso negado. Esta aula pertence a outro professor.');
    }
        
       
        $validated = $request->validate([
            'justification' => 'required|string',
        ]);

        $lesson->update([
            'status' => 'canceled',
            'justification' => $validated['justification']
        ]);

        return back()->with('success', 'Aula cancelada com sucesso!');
    }

    public function register(Request $request, Lesson $lesson)
    {
        $user = auth()->user();

        // 🛡️ CHAVE-MESTRA:
        if ($user->role === 'teacher' && $lesson->classroom->teacher_id !== $user->id) {
            abort(403, 'Acesso negado. Esta aula pertence a outro professor.');
        }

        // Validação expandida para os novos campos de LMS
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string', // A descrição/registro da aula
            'video_url' => 'nullable|url|max:255',
            'material' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx|max:10240', // Max 10MB
            'xp_reward' => 'nullable|integer|min:0',
            'activity_ids' => 'nullable|array',
            'activity_ids.*' => 'exists:activities,id',
        ]);

        $dataToUpdate = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'video_url' => $validated['video_url'] ?? null,
            'xp_reward' => $validated['xp_reward'] ?? 0,
        ];

        // Se o professor enviou um arquivo, salvamos no Storage
        if ($request->hasFile('material')) {
            // (Opcional) Deleta o arquivo antigo se existir para economizar espaço
            if ($lesson->main_material_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($lesson->main_material_path);
            }
            
            // Salva na pasta storage/app/public/lessons/materials
            $path = $request->file('material')->store('lessons/materials', 'public');
            $dataToUpdate['main_material_path'] = $path;
        }

        $lesson->update($dataToUpdate);

        \App\Models\Activity::where('lesson_id', $lesson->id)->update(['lesson_id' => null]);

        if (!empty($validated['activity_ids'])) {
            \App\Models\Activity::whereIn('id', $validated['activity_ids'])
                ->where('classroom_id', $lesson->classroom_id) 
                ->update(['lesson_id' => $lesson->id]);
        }

        return back()->with('success', 'Aula configurada e registrada com sucesso!');
    }
}
