<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    /**
     * Listagem de turmas da instituição
     */
    public function index(Request $request)
    {
        // Busca as turmas da instituição do usuário logado
        $query = Classroom::where('institution_id', $request->user()->institution_id);

        // Sistema de Filtro (Busca)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Carrega o professor (user) e conta os alunos matriculados
        $classrooms = $query->with('teacher')->withCount('students')->paginate(15);

        return view('classrooms.index', compact('classrooms'));
    }

    /**
     * Tela de criação de nova turma (Com trava de limite)
     */
    public function create()
    {
        $institution = auth()->user()->institution;

        // 🛡️ TRAVA: Se o plano não permitir mais turmas, barra aqui.
        if (!$institution->canCreate('classes')) {
            return redirect()->route('classrooms.index')
                ->with('error', '🚫 Limite de turmas atingido para o seu plano atual. Faça um upgrade para criar novas turmas!');
        }

        return view('classrooms.create');
    }

    /**
     * Salvar a nova turma no banco (Com trava de segurança)
     */
    public function store(Request $request)
    {
        $institution = $request->user()->institution;

        // 🛡️ TRAVA: Segurança extra caso tentem burlar o formulário
        if (!$institution->canCreate('classes')) {
            return redirect()->route('classrooms.index')
                ->with('error', '🚫 Operação negada: Limite de turmas excedido.');
        }

        // 1. Validamos os dados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'base_xp_level' => 'nullable|numeric',
            'level_growth_factor' => 'nullable|numeric',
            'total_lessons' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'min_attendance_percent' => 'nullable|numeric',
            'frequency' => 'required|string',
            'days_of_week' => 'nullable|array',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ]);

        // 2. Gerador do Código de Convite Único
        $joinCode = Str::upper(Str::random(6));
        while (Classroom::where('join_code', $joinCode)->exists()) {
            $joinCode = Str::upper(Str::random(6));
        }

        // 3. Criação da Turma
        $classroom = Classroom::create([
            'institution_id' => $institution->id,
            'teacher_id' => $request->user()->id,
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'join_code' => $joinCode,
            'base_xp_level' => $validated['base_xp_level'] ?? 100,
            'level_growth_factor' => $validated['level_growth_factor'] ?? 1.20,
            'total_lessons' => $validated['total_lessons'],
            'start_date' => $validated['start_date'],
            'min_attendance_percent' => $validated['min_attendance_percent'] ?? 70,
            'frequency' => $validated['frequency'],
            'days_of_week' => $validated['days_of_week'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'skip_holidays' => $request->has('skip_holidays'),
        ]);

        // 4. Gera as aulas no calendário
        $classroom->generateLessons();

        return redirect()->route('classrooms.index')
                         ->with('success', 'Turma e Calendário criados com sucesso!');
    }

    public function show(Classroom $classroom)
    {
        if ($classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Carregamos os alunos, as aulas (ordenadas) e as atividades
        $classroom->load([
            'students',
            'lessons' => function ($query) {
                $query->orderBy('date', 'asc')->orderBy('start_time', 'asc')->with('attendances');
            },
            'activities'
        ]);

        return view('classrooms.show', compact('classroom'));
    }

    public function edit(Classroom $classroom)
    {
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }
        return view('classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'total_lessons' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'min_attendance_percent' => 'nullable|numeric',
            'frequency' => 'required|string',
            'days_of_week' => 'nullable|array',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ]);

        $validated['skip_holidays'] = $request->has('skip_holidays');

        $classroom->update($validated);

        // Regenera as aulas se houver mudanças no cronograma
        $classroom->generateLessons();

        return redirect()->route('classrooms.index')
                         ->with('success', 'Turma atualizada e cronograma regenerado com sucesso!');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }

        $classroom->delete();

        return redirect()->route('classrooms.index')
                         ->with('success', 'Turma removida com sucesso!');
    }
}