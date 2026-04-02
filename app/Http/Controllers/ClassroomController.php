<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        // Busca as turmas da instituição do usuário logado
        $query = \App\Models\Classroom::where('institution_id', $request->user()->institution_id);

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

    public function create()
    {
        return view('classrooms.create');
    }

    public function store(Request $request)
    {
        // 1. Validamos TUDO (Gamificação + Calendário)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'base_xp_level' => 'nullable|numeric',
            'level_growth_factor' => 'nullable|numeric',
            
            // Campos de Calendário
            'total_lessons' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'min_attendance_percent' => 'nullable|numeric',
            'frequency' => 'required|string',
            'days_of_week' => 'nullable|array',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ]);

        // 2. Gerador do Código de Convite da Turma
        $joinCode = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));
        while (\App\Models\Classroom::where('join_code', $joinCode)->exists()) {
            $joinCode = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));
        }

        // 3. Cria a turma uma única vez, juntando todos os dados
        $classroom = \App\Models\Classroom::create([
            'institution_id' => $request->user()->institution_id,
            'teacher_id' => $request->user()->id,
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            
            // Dados de Gamificação
            'join_code' => $joinCode,
            'base_xp_level' => $validated['base_xp_level'] ?? 100,
            'level_growth_factor' => $validated['level_growth_factor'] ?? 1.20,
            
            // Dados do Calendário
            'total_lessons' => $validated['total_lessons'],
            'start_date' => $validated['start_date'],
            'min_attendance_percent' => $validated['min_attendance_percent'] ?? 70,
            'frequency' => $validated['frequency'],
            'days_of_week' => $validated['days_of_week'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'skip_holidays' => $request->has('skip_holidays'),
        ]);

        // 4. MÁGICA: Gera as aulas no banco de dados
        $classroom->generateLessons();

        // 5. Redireciona de volta
        return redirect()->route('classrooms.index')->with('success', 'Turma e Calendário criados com sucesso!');
    }
    public function show(Classroom $classroom)
    {
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }
        $classroom->load('activities');
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
        // 1. Validamos todos os campos novos que vêm do formulário
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

        // 2. O checkbox envia "1" se marcado, ou NADA se desmarcado. Tratamos isso:
        $validated['skip_holidays'] = $request->has('skip_holidays');

        // 3. Salvamos no banco
        $classroom->update($validated);

        /* * NOTA: Neste momento, estamos apenas atualizando os dados da turma.
         * Futuramente, se o professor mudar os dias no meio do curso, 
         * precisaremos criar uma lógica para recalcular apenas as "aulas futuras" no calendário.
         */

        return redirect()->route('classrooms.index')->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }

        $classroom->delete();

        return redirect()->route('classrooms.index')->with('success', 'Turma removida com sucesso!');
    }
}
