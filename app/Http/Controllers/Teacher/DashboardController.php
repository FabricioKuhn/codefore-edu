<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\User;
use App\Models\Activity;
use App\Models\Submission;
use App\Models\Lesson;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // DASHBOARD DO PROFESSOR
    public function teacherIndex()
    {
        $data = $this->getSharedDashboardData();
        return view('teacher.dashboard', $data);
    }

    // DASHBOARD DO ADMIN DA ESCOLA
    public function adminIndex()
    {
        $data = $this->getSharedDashboardData();
        // Caso queira adicionar algo específico pro admin no futuro:
        // $data['totalProfessores'] = User::where('institution_id', $data['tenant']->id)->where('role', 'teacher')->count();
        
        return view('admin.dashboard', $data);
    }

    public function studentIndex()
{
    $user = auth()->user();
    $tenant = $user->institution;
    
    // Carrega as turmas para usarmos na View
    $user->load('classrooms');
    $classroomIds = $user->classrooms->pluck('id');

    // 1. Total XP (Submissões já avaliadas)
    $totalXp = \App\Models\Submission::where('student_id', $user->id)
        ->where('status', 'evaluated')
        ->sum('earned_xp');

    // 2. Tarefas Disponíveis (Apenas as que o aluno AINDA NÃO respondeu)
    $atividades = \App\Models\Activity::whereIn('classroom_id', $classroomIds)
        ->where('status', 'active') // Só atividades ativas
        ->whereDoesntHave('submissions', function($query) use ($user) {
            // Se existir uma submissão deste aluno para esta atividade, a atividade NÃO aparece
            $query->where('student_id', $user->id); 
        })
        ->with('classroom')
        ->get();

    // 3. Calendário (Aulas e Prazos)
    $eventos = [];

    // Aulas (Garantido: Somente das turmas do aluno)
    // Buscar Aulas (Garantido: Somente das turmas do aluno)
    $aulas = \App\Models\Lesson::whereIn('classroom_id', $classroomIds)
        ->where('status', '!=', 'canceled')
        ->with('classroom')
        ->get();

    foreach ($aulas as $aula) {
        $eventos[] = [
            'title' => "📖 Aula: {$aula->title}",
            'start' => $aula->date->format('Y-m-d') . 'T' . ($aula->start_time ?? '00:00:00'),
            'description' => "Turma: {$aula->classroom->name}",
            'color' => 'var(--secondary-color)',
            // ✅ ADICIONADO: URL para a sala da turma
            'url' => route('student.classrooms.show', $aula->classroom_id) 
        ];
    }

    // Prazos das Atividades
    foreach ($atividades as $atv) {
        if ($atv->end_date) {
            $eventos[] = [
                'title' => "🚩 Prazo: {$atv->title}",
                'start' => $atv->end_date->format('Y-m-d\TH:i:s'),
                'description' => "Entrega final para {$atv->classroom->name}",
                'color' => 'var(--primary-color)',
                // ✅ ADICIONADO: URL para a atividade específica (ou para a sala, se preferir)
                'url' => route('student.activities.show', $atv->id) 
            ];
        }
    }

    // Prazos das Atividades
    foreach ($atividades as $atv) {
        if ($atv->end_date) {
            $eventos[] = [
                'title' => "🚩 Prazo: {$atv->title}",
                'start' => $atv->end_date->format('Y-m-d\TH:i:s'),
                'description' => "Entrega final para {$atv->classroom->name}",
                'color' => 'var(--primary-color)',
            ];
        }
    }

    return view('student.dashboard', compact(
        'user', 'tenant', 'totalXp', 'atividades', 'eventos'
    ));
}

    private function getSharedDashboardData()
    {
        $user = auth()->user();
        $tenant = $user->institution;
        $institutionId = $tenant->id;
        $trintaDiasAtras = now()->subDays(30);

        return [
            'tenant' => $tenant,
            'totalTurmas' => Classroom::where('institution_id', $institutionId)->count(),
            'totalAlunos' => User::where('institution_id', $institutionId)->where('role', 'student')->count(),
            'xpGerada' => Submission::whereHas('activity.classroom', fn($q) => $q->where('institution_id', $institutionId))
                            ->where('status', 'evaluated')
                            ->where('evaluated_at', '>=', $trintaDiasAtras)
                            ->sum('earned_xp'),
            'novosAlunos' => User::where('institution_id', $institutionId)
                            ->where('role', 'student')
                            ->where('created_at', '>=', $trintaDiasAtras)
                            ->count(),
            'correcoesPendentes' => Activity::with(['classroom'])
                            ->withCount(['submissions' => fn($q) => $q->where('status', 'waiting_evaluation')])
                            ->whereHas('classroom', fn($q) => $q->where('institution_id', $institutionId))
                            ->having('submissions_count', '>', 0)
                            ->get(),
            'eventos' => $this->getCalendarEvents($institutionId, $tenant)
        ];
    }

    private function getCalendarEvents($institutionId, $tenant)
    {
        $eventos = [];

        // 1. Prazos de Atividades
        $atividades = Activity::with('classroom')
            ->whereHas('classroom', fn($q) => $q->where('institution_id', $institutionId))
            ->whereNotNull('end_date')->get();
            
        foreach($atividades as $atv) {
            $eventos[] = [
                'title' => "📌 PRAZO: {$atv->title}",
                'start' => $atv->end_date->format('Y-m-d\TH:i:s'),
                'description' => "Turma: {$atv->classroom->name}",
                'color' => $tenant->primary_color ?? '#00ad9a',
            ];
        }

        // 2. Aulas
        $aulas = Lesson::with('classroom')
            ->whereHas('classroom', fn($q) => $q->where('institution_id', $institutionId))->get();

        foreach($aulas as $aula) {
            $isCancelada = ($aula->status === 'canceled');
            $eventos[] = [
                'title' => ($isCancelada ? "❌ [CANCELADA] " : "📖 AULA: ") . $aula->title,
                'start' => $aula->date->format('Y-m-d') . 'T' . ($aula->start_time ?? '00:00:00'),
                'description' => $isCancelada ? "AULA CANCELADA" : "Turma: {$aula->classroom->name}",
                'color' => $isCancelada ? '#94a3b8' : ($tenant->secondary_color ?? '#333333'),
            ];
        }

        return $eventos;
    }
}