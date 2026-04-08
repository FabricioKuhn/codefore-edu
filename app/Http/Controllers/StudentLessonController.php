<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Lesson;
use Illuminate\Http\Request;

class StudentLessonController extends Controller
{
    public function show(Classroom $classroom, Lesson $lesson)
    {
        // 🌟 CORREÇÃO AQUI: Apontando explicitamente para users.id
        abort_if(!$classroom->students()->where('users.id', auth()->id())->exists(), 403);
        
        // 🌟 CORREÇÃO AQUI: users.id
        $isCompleted = $lesson->studentsCompleted()->where('users.id', auth()->id())->exists();

        return view('student.lessons.show', compact('classroom', 'lesson', 'isCompleted'));
    }

    public function complete(Request $request, Classroom $classroom, Lesson $lesson)
    {
        $user = auth()->user();

        // 🌟 CORREÇÃO AQUI: users.id
        if (!$lesson->studentsCompleted()->where('users.id', $user->id)->exists()) {
            
            // Registra a conclusão na tabela pivot (lesson_student)
            $lesson->studentsCompleted()->attach($user->id, ['completed_at' => now()]);
            
            // (Futuro) Aqui nós vamos injetar a lógica de somar o XP no perfil do aluno!
            
            return back()->with('success', "Aula concluída! Você ganhou {$lesson->xp_reward} XP.");
        }

        return back();
    }
}