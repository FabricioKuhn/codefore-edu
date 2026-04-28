<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Lesson;


class ClassroomController extends Controller
{
    /**
     * Listagem de turmas da instituição
     */
    // Lembre-se de importar o Inertia no topo do arquivo se já não estiver lá:
// use Inertia\Inertia;

public function index(Request $request)
{
    $user = auth()->user();

    // 1. Inicia a busca pegando apenas turmas desta Instituição
    $query = \App\Models\Classroom::where('institution_id', $user->institution_id);

    // 2. Filtro de Busca Avançada (ID, Nome, Matéria ou Professor)
    if ($request->filled('search')) {
        $term = $request->search;
        $query->where(function($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('subject', 'like', '%' . $term . '%')
              ->orWhere('id', 'like', '%' . $term . '%')
              // O orWhereHas pesquisa dentro do relacionamento (tabela users)
              ->orWhereHas('teacher', function($tQuery) use ($term) {
                  $tQuery->where('name', 'like', '%' . $term . '%');
              });
        });
    }

    // 3. Se for Professor, filtra só as turmas DELE
    if ($user->role === 'teacher') {
        $query->where('teacher_id', $user->id);
    }

    // 4. Executa a busca com paginação e count, INCLUINDO O PROFESSOR!
    $classrooms = $query->with('teacher')->withCount('students')->paginate(10)->withQueryString();

    // ✅ MUDANÇA: Retornando via Inertia
    return Inertia::render('Admin/Classrooms/Index', [
        'classrooms' => $classrooms,
        'filters' => $request->only('search') // Envia o termo buscado de volta pra tela
    ]);
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

        // RETORNA PARA A TELA VUE
        return Inertia::render('Admin/Classrooms/Create', [
            'teachers' => $teachers
        ]);
    }

    /**
     * Salvar a nova turma no banco (Com trava de segurança)
     */
    public function store(Request $request)
{
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
    // ✅ CORREÇÃO: Usamos o 'teacher_id' que veio do formulário ($validated)
    $classroom = Classroom::create([
        'institution_id' => $institution->id,
        'teacher_id' => $validated['teacher_id'], 
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
    $user = auth()->user();

    // Carrega os relacionamentos necessários
    $classroom->load(['teacher', 'students']);

    // Busca as aulas (Lessons) ordenadas
    $lessons = $classroom->lessons()
        ->orderBy('date', 'asc')
        ->orderBy('start_time', 'asc')
        ->get();

    // Busca as atividades (Activities)
    $activities = $classroom->activities()
        ->orderBy('created_at', 'desc')
        ->get();

    // Busca alunos da mesma instituição que AINDA NÃO estão nesta turma (para o modal de adicionar aluno)
    $availableStudents = \App\Models\User::where('role', 'student')
        ->where('institution_id', $user->institution_id)
        ->whereDoesntHave('classrooms', function ($query) use ($classroom) {
            $query->where('classrooms.id', $classroom->id);
        })
        ->orderBy('name')
        ->get();

    return Inertia::render('Admin/Classrooms/Show', [
        'classroom' => $classroom,
        'lessons' => $lessons,
        'activities' => $activities,
        'availableStudents' => $availableStudents
    ]);
}

    public function edit(Classroom $classroom)
    {
        $user = auth()->user();

        // Trava de segurança para professores
        if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
            abort(403);
        }
        
        $teachers = \App\Models\User::where('role', 'teacher')
                        ->where('institution_id', $user->institution_id)
                        ->get();

        return Inertia::render('Admin/Classrooms/Edit', [
            'classroom' => $classroom,
            'teachers' => $teachers
        ]);
    }

    public function update(Request $request, Classroom $classroom)
    {
        $user = auth()->user();

        if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
            abort(403);
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
            'base_xp_level' => 'nullable|numeric',
            'level_growth_factor' => 'nullable|numeric',
        ]);

        // Tratamento para campos booleanos e arrays
        $validated['skip_holidays'] = $request->boolean('skip_holidays');
        $validated['days_of_week'] = $request->input('days_of_week', []);

        $classroom->update($validated);

        // Opcional: Regenerar aulas se houver mudança crítica? 
        // Se quiser automatizar isso: $classroom->generateLessons();

        return redirect()->route($user->role . '.classrooms.index')
                         ->with('success', 'Turma atualizada com sucesso!');
    }

    public function addManualLessons(Request $request, Classroom $classroom)
{
    $request->validate([
        'lessons' => 'required|array|min:1',
        'lessons.*.title' => 'required|string|max:255',
        'lessons.*.date' => 'required|date',
        'lessons.*.start_time' => 'required',
        'lessons.*.end_time' => 'required|after:lessons.*.start_time',
    ]);

    foreach ($request->lessons as $lessonData) {
        // ✨ VERIFICAÇÃO DE CONFLITO ✨
        // Verifica se já existe uma aula PARA ESTA TURMA na mesma data e com sobreposição de horário
        $conflict = $classroom->lessons()
            ->where('date', $lessonData['date'])
            ->where(function ($query) use ($lessonData) {
                $query->where(function ($q) use ($lessonData) {
                    $q->where('start_time', '<', $lessonData['end_time'])
                      ->where('end_time', '>', $lessonData['start_time']);
                });
            })
            ->first();

        if ($conflict) {
            return back()->withErrors([
                'conflict' => "Conflito detectado: A aula \"{$lessonData['title']}\" no dia " . date('d/m', strtotime($lessonData['date'])) . " coincide com a aula \"{$conflict->title}\" ({$conflict->start_time} - {$conflict->end_time})."
            ]);
        }

        // Se não houver conflito, cria a aula
        $classroom->lessons()->create([
            'title'      => $lessonData['title'],
            'date'       => $lessonData['date'],
            'start_time' => $lessonData['start_time'],
            'end_time'   => $lessonData['end_time'],
            'status'     => 'scheduled',
            'is_active'  => true,
        ]);
    }

    return back()->with('success', 'Aulas adicionadas com sucesso!');
}

    public function destroy(Classroom $classroom)
{
    // Pega o status atual e inverte
    $novoStatus = !$classroom->is_active;

    // 1. Atualiza a Turma
    $classroom->update(['is_active' => $novoStatus]);

    // 2. Atualiza as Aulas (Lessons) - Aqui usamos o relacionamento
    if (method_exists($classroom, 'lessons')) {
        $classroom->lessons()->update(['is_active' => $novoStatus]);
    }

    // 3. Atualiza as Atividades (Activities) - Aqui usamos o relacionamento
    if (method_exists($classroom, 'activities')) {
        $classroom->activities()->update(['is_active' => $novoStatus]);
    }

    $msg = $novoStatus ? 'Turma reativada com sucesso!' : 'Turma inativada com sucesso!';
    
    return back()->with('success', $msg);
}
}