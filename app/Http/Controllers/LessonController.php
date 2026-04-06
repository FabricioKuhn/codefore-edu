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
    // Só bloqueia se o usuário for um PROFESSOR e a aula não for dele.
    // Se for ADMIN, ele pula esse 'if' e executa a ação.
    if ($user->role === 'teacher' && $lesson->classroom->teacher_id !== $user->id) {
        abort(403, 'Acesso negado. Esta aula pertence a outro professor.');
    }
        
        

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $lesson->update([
            'status' => 'recorded', // Mantém 'recorded' para consistência com os badges
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Registro de aula salvo com sucesso!');
    }
}
