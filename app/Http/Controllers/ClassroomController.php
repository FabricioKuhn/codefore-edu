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
    public function index()
    {
        $user = auth()->user();

        // 1. Inicia a busca pegando apenas turmas desta Instituição
        $query = \App\Models\Classroom::where('institution_id', $user->institution_id);

        // 2. Se for Professor, filtra só as turmas DELE
        if ($user->role === 'teacher') {
            $query->where('teacher_id', $user->id);
        }

        // 3. Executa a busca (verifique se você usava ->get() ou ->paginate() e ajuste se necessário)
        // Aqui estou incluindo a contagem de alunos para não quebrar a tabela
        $classrooms = $query->withCount('students')->paginate(10); 

        return view('classrooms.index', compact('classrooms'));
    }

    /**
     * Tela de criação de nova turma (Com trava de limite)
     */
    public function create()
    {
        // Busca os professores que pertencem à escola logada
        $teachers = \App\Models\User::where('role', 'teacher')
                        ->where('institution_id', auth()->user()->institution_id)
                        ->get();

        // Envia a variável $teachers para a tela
        return view('classrooms.create', compact('teachers'));
    }

    /**
     * Salvar a nova turma no banco (Com trava de segurança)
     */
    public function store(Request $request)
    {

        if (auth()->user()->role === 'teacher' && $classroom->teacher_id !== auth()->id()) {
        abort(403, 'Você não tem permissão para gerenciar os alunos desta turma.');
    }

        $institution = $request->user()->institution;

        // 🛡️ TRAVA: Segurança extra caso tentem burlar o formulário
        if (!$institution->canCreate('classes')) {
            return redirect()->route(auth()->user()->role . '.classrooms.index')
                ->with('error', '🚫 Operação negada: Limite de turmas excedido.');
        }

        // 1. Validamos os dados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
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
            'teacher_id' => $request->teacher()->id,
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

        return redirect()->route(auth()->user()->role . '.classrooms.index')
                         ->with('success', 'Turma e Calendário criados com sucesso!');
    }

    public function show(Classroom $classroom)
    {

  

    // Busca alunos da mesma escola que NÃO estão nesta turma
    $availableStudents = \App\Models\User::where('role', 'student')
        ->where('institution_id', auth()->user()->institution_id)
        ->whereDoesntHave('classrooms', function ($query) use ($classroom) {
            $query->where('classrooms.id', $classroom->id);
        })
        ->orderBy('name')
        ->get();

        $user = auth()->user();

        // Se for professor E a turma não for dele, bloqueia. (Admin passa direto!)
        if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
            abort(403, 'Acesso negado. Você não é o professor desta turma.');
        }

        // Carregamos os alunos, as aulas (ordenadas) e as atividades
        $classroom->load([
            'students',
            'lessons' => function ($query) {
                $query->orderBy('date', 'asc')->orderBy('start_time', 'asc')->with('attendances');
            },
            'activities'
        ]);

        return view('classrooms.show', compact('classroom', 'availableStudents'));
    }

    public function edit(Classroom $classroom)
    {
        $user = auth()->user();

        // Se for professor E a turma não for dele, bloqueia
        if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
            abort(403, 'Acesso negado. Você só pode editar as suas próprias turmas.');
        }
        
        // Busca os professores
        $teachers = \App\Models\User::where('role', 'teacher')
                        ->where('institution_id', auth()->user()->institution_id)
                        ->get();

        // Envia a turma ($classroom) E os professores ($teachers) para a tela
        return view('classrooms.edit', compact('classroom', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom)
    {

        $user = auth()->user();

        // Se for professor E a turma não for dele, bloqueia
        if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
            abort(403, 'Acesso negado. Você só pode editar as suas próprias turmas.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
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

        return redirect()->route(auth()->user()->role . '.classrooms.index')
                         ->with('success', 'Turma atualizada e cronograma regenerado com sucesso!');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }

        $classroom->delete();

        return redirect()->route(auth()->user()->role . '.classrooms.index')
                         ->with('success', 'Turma removida com sucesso!');
    }
}