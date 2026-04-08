<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class StudentClassroomController extends Controller
{
    public function show(\App\Models\Classroom $classroom)
    {
        $studentId = auth()->id();

        // Garante que o aluno tem acesso a esta turma
        abort_if(!$classroom->students()->where('users.id', $studentId)->exists(), 403);

        // ==========================================
        // CÁLCULO DO PROGRESSO (A "PLATINA")
        // ==========================================

        // 1. Progresso de Aulas Concluídas
        $totalLessons = $classroom->lessons->count();
        $completedLessons = $classroom->lessons()->whereHas('studentsCompleted', function($q) use ($studentId) {
            $q->where('users.id', $studentId);
        })->count();
        $lessonProgress = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

        // 2. Progresso de Missões
        // Considera apenas atividades 'active' ou 'closed' (ignora drafts)
        $totalActivities = $classroom->activities()->whereIn('status', ['active', 'closed'])->count();
        $completedActivities = $classroom->activities()->whereHas('submissions', function($q) use ($studentId) {
            $q->where('student_id', $studentId)->whereIn('status', ['evaluated', 'completed']);
        })->count();
        $activityProgress = $totalActivities > 0 ? ($completedActivities / $totalActivities) * 100 : 0;

        // 3. Frequência (Presenças vs Aulas Registradas)
        $recordedLessons = $classroom->lessons()->where('status', 'recorded')->count();
        $presences = $classroom->lessons()->whereHas('attendances', function($q) use ($studentId) {
            $q->where('user_id', $studentId)->where('status', 'present');
        })->count();
        // Se o curso acabou de começar e não tem chamada, a frequência é 100% por padrão.
        $attendanceProgress = $recordedLessons > 0 ? ($presences / $recordedLessons) * 100 : 100;

        // Média Final Arredondada
        if ($totalLessons == 0 && $totalActivities == 0) {
            $progress = 0;
        } else {
            $progress = round(($lessonProgress + $activityProgress + $attendanceProgress) / 3);
        }

        return view('student.classrooms.show', compact('classroom', 'progress'));
    }
}
