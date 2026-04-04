<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function storeAttendance(Request $request, Lesson $lesson)
    {
        if ($lesson->classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,justified',
        ]);

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
        if ($lesson->classroom->teacher_id !== auth()->id()) {
            abort(403);
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
        if ($lesson->classroom->teacher_id !== auth()->id()) {
            abort(403);
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
