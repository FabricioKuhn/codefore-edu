<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')],
            ['name' => 'Minhas Turmas', 'url' => route(auth()->user()->role . '.classrooms.index')],
            ['name' => $activity->classroom->name, 'url' => route(auth()->user()->role . '.classrooms.show', $activity->classroom)],
            ['name' => $activity->title, 'url' => '#']
        ]" />
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-2 gap-4">
            <h2 class="text-xl font-semibold text-secondary leading-tight flex items-center gap-3">
                <span class="bg-gray-800 text-white px-3 py-1 rounded text-[10px] uppercase font-black tracking-widest">
                    {{ $activity->type === 'exam' ? 'Prova' : 'Tarefa' }}
                </span>
                {{ $activity->title }}
            </h2>
            
            <div class="flex items-center gap-3" x-data>
                <x-primary-button type="button" @click="$dispatch('open-import-modal')" class="bg-gray-800 hover:bg-gray-700">
                    + Vincular do Banco
                </x-primary-button>
                <a href="{{ route(auth()->user()->role . '.questions.create') }}">
                    <x-primary-button type="button" class="bg-primary hover:brightness-110">
                        Nova Questão
                    </x-primary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
    lightboxOpen: false, 
    lightboxImg: '', 
    importModalOpen: false,
    deadlineModalOpen: false,
    selectedStudent: { name: '', id: '', currentDeadline: '' },
    deadlineAction: ''
}" @open-import-modal.window="importModalOpen = true">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-6 bg-white shadow-sm sm:rounded-xl border border-gray-100 p-8">
                    <div class="flex flex-col lg:flex-row justify-between items-start mb-6">
                        <div class="max-w-3xl">
                            <h3 class="text-2xl font-black text-secondary">{{ $activity->title }}</h3>
                            <p class="text-gray-500 mt-2 font-medium leading-relaxed">{{ $activity->description ?? 'Sem descrição fornecida.' }}</p>
                        </div>
                        <div class="mt-4 lg:mt-0 flex gap-4">
                            <div class="text-center bg-gray-50 px-6 py-4 rounded-xl border border-gray-100">
                                <span class="text-[10px] uppercase text-gray-400 font-black tracking-widest block mb-1">XP Base</span>
                                <div class="text-2xl font-black text-primary">{{ $activity->base_xp }}</div>
                            </div>
                            @if($activity->time_limit_minutes)
                            <div class="text-center bg-gray-50 px-6 py-4 rounded-xl border border-gray-100">
                                <span class="text-[10px] uppercase text-gray-400 font-black tracking-widest block mb-1">Tempo Limite</span>
                                <div class="text-2xl font-black text-secondary">{{ $activity->time_limit_minutes }}m</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-between">
                        <div class="flex gap-8">
                            <div>
                                <span class="text-[10px] uppercase font-black text-gray-400 tracking-widest block mb-1">Status Atual</span>
                                <span class="text-sm font-bold {{ $activity->status === 'active' ? 'text-green-600' : 'text-gray-700' }} uppercase">
                                    {{ $activity->status_label }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-black text-gray-400 tracking-widest block mb-1">Início</span>
                                <span class="text-sm font-bold text-secondary">{{ $activity->start_date ? $activity->start_date->format('d/m/Y H:i') : 'Não definido' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-black text-gray-400 tracking-widest block mb-1">Prazo Final</span>
                                <span class="text-sm font-bold text-secondary">{{ $activity->end_date ? $activity->end_date->format('d/m/Y H:i') : 'Não definido' }}</span>
                            </div>
                        </div>
                        <a href="{{ route(auth()->user()->role . '.activities.edit', $activity) }}" class="text-primary hover:underline text-xs font-black uppercase tracking-widest">
                            Editar Configurações
                        </a>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4 mt-10 px-2">
                    <h3 class="text-xl font-black text-secondary uppercase tracking-widest">
                        {{ $activity->type === 'exam' ? 'Pool de Questões (Sorteio)' : 'Questões da Tarefa' }}
                    </h3>
                </div>

                @if($activity->type === 'exam')
                    <div class="bg-purple-50 border border-purple-100 rounded-xl p-4 mb-6 flex gap-6 text-sm">
                        <div><strong class="text-purple-800">Sorteio Definido:</strong></div>
                        <div class="text-purple-700">{{ $activity->exam_settings['multiple_choice'] ?? 0 }} de Múltipla Escolha</div>
                        <div class="text-purple-700">{{ $activity->exam_settings['descriptive'] ?? 0 }} Descritivas</div>
                        <div class="text-xs text-purple-500 ml-auto pt-1">(Os alunos receberão aleatoriamente dessa quantidade a partir das questões abaixo)</div>
                    </div>
                @endif
                
                <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden mb-12">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ordem</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Enunciado</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tipo</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Peso Nesta Prova</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($activity->questions as $index => $question)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-black text-gray-300 text-xs">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
    {{-- Enunciado --}}
    <div class="text-sm font-bold text-secondary line-clamp-2" title="{{ strip_tags($question->statement) }}">
        {!! strip_tags($question->statement) !!}
    </div>
    
    {{-- Linha de informações: ID + Tags --}}
    <div class="flex flex-wrap items-center gap-2 mt-2">
        <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest shrink-0">
            ID Banco: #{{ $question->id }}
        </span>

        @if($question->tags && count($question->tags) > 0)
            <div class="flex flex-wrap gap-1">
                @foreach($question->tags as $tag)
                    <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest border border-gray-200">
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest {{ $question->type === 'multiple_choice' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                                        {{ $question->type === 'multiple_choice' ? 'Múltipla Escolha' : 'Descritiva' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Formulário para alterar o peso SÓ NESTA PROVA --}}
                                    <form action="{{ route(auth()->user()->role . '.activities.questions.update_weight', [$activity, $question]) }}" method="POST" class="flex items-center justify-center gap-2">
                                        @csrf @method('PATCH')
                                        <input type="number" name="weight" value="{{ $question->pivot->weight_override ?? $question->default_weight }}" class="w-16 text-center text-xs font-bold border-gray-200 rounded py-1 px-2 focus:ring-primary focus:border-primary" min="1">
                                        <button type="submit" class="text-[10px] text-gray-400 hover:text-primary uppercase font-black" title="Salvar Novo Peso">OK</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route(auth()->user()->role . '.activities.questions.detach', [$activity, $question]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 font-black text-[10px] uppercase tracking-widest transition">Remover</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-gray-400 font-bold text-sm mb-4">Nenhuma questão vinculada a esta avaliação ainda.</p>
                                    <button @click="$dispatch('open-import-modal')" class="text-primary hover:underline text-xs font-black uppercase tracking-widest">Vincular do Banco</button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between mb-4 px-2">
                    <h3 class="text-xl font-black text-secondary uppercase tracking-widest">Painel de Alunos e Avaliação</h3>
                </div>
                
                <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aluno</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Prazo Individual</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Nota Final</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($activity->classroom->students ?? [] as $student)
                            @php
                                // Busca o controle do aluno na tabela submissions
                                $submission = $activity->submissions->where('student_id', $student->id)->first();
                                $isEnabled = $submission ? $submission->is_enabled : true;
                                $status = $submission ? $submission->status : 'pending';
                                $deadline = $submission && $submission->custom_deadline ? $submission->custom_deadline->format('d/m/Y H:i') : 'Padrão';
                            @endphp
                            
                            <tr class="hover:bg-gray-50/50 transition {{ !$isEnabled ? 'bg-gray-50 opacity-60' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-secondary {{ !$isEnabled ? 'line-through text-gray-400' : '' }}">{{ $student->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-1">{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    @if(!$isEnabled)
                                        <span class="bg-red-50 text-red-600 px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest border border-red-100">Desabilitado</span>
                                    @else
                                        @switch($status)
                                            @case('pending')
                                                <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest">Aguardando Início</span>
                                                @break
                                            @case('in_progress')
                                                <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest border border-blue-100 flex items-center justify-center gap-1">
                                                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span> Em Andamento
                                                </span>
                                                @break
                                            @case('waiting_evaluation')
                                                <span class="bg-yellow-50 text-yellow-600 px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest border border-yellow-100">Avaliar</span>
                                                @break
                                            @case('evaluated')
                                                <span class="bg-green-50 text-green-600 px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest border border-green-100">Avaliado</span>
                                                @break
                                        @endswitch
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-center text-xs font-bold {{ $deadline !== 'Padrão' ? 'text-primary' : 'text-gray-400' }}">
                                    {{ $deadline }}
                                </td>
                                
                                <td class="px-6 py-4 text-center font-black text-secondary">
                                    @if($status === 'evaluated')
                                        {{ $submission->earned_xp ?? 0 }} XP
                                    @else
                                        -
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                                    @if($status === 'waiting_evaluation')
                                        <button type="button" class="text-primary hover:text-blue-700 font-black text-[10px] uppercase tracking-widest transition" title="Corrigir Prova">Corrigir</button>
                                    @elseif($status === 'evaluated')
                                        <button type="button" class="text-gray-400 hover:text-primary font-black text-[10px] uppercase tracking-widest transition" title="Ver Gabarito Final">Gabarito</button>
                                    @endif
                                    
                                    <button type="button" 
    @click="
        selectedStudent = { 
            name: '{{ $student->name }}', 
            id: '{{ $student->id }}', 
            currentDeadline: '{{ $submission && $submission->custom_deadline ? $submission->custom_deadline->format('Y-m-d\TH:i') : '' }}' 
        };
        deadlineAction = '{{ route(auth()->user()->role . '.activities.students.deadline', [$activity, $student]) }}';
        deadlineModalOpen = true;
    "
    class="text-gray-400 hover:text-blue-500 font-black text-[10px] uppercase tracking-widest transition" 
    title="Estender Prazo">
    Prazo
</button>
                                    
                                    <form action="{{ route(auth()->user()->role . '.activities.students.toggle', [$activity, $student]) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="{{ $isEnabled ? 'text-gray-400 hover:text-red-500' : 'text-green-500 hover:text-green-700' }} font-black text-[10px] uppercase tracking-widest transition">
                                            {{ $isEnabled ? 'Ocultar' : 'Habilitar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-bold text-sm">Nenhum aluno matriculado nesta turma.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div x-show="importModalOpen" 
     x-cloak 
     x-data="{ 
        search: '',
        // Transformamos a coleção do PHP em um objeto JS para o Alpine
        allQuestions: {{ json_encode(\App\Models\Question::where('institution_id', auth()->user()->institution_id)
                            ->where('status', true)
                            ->whereNotIn('id', $activity->questions->pluck('id'))
                            ->latest()
                            ->get()) }},
        
        // Função que faz o filtro dinâmico
        get filteredQuestions() {
            if (this.search === '') return this.allQuestions;
            
            const s = this.search.toLowerCase();
            return this.allQuestions.filter(q => {
                const statementMatch = q.statement.toLowerCase().includes(s);
                // Verifica se alguma tag contém o termo de busca
                const tagsMatch = q.tags ? q.tags.some(tag => tag.toLowerCase().includes(s)) : false;
                
                return statementMatch || tagsMatch;
            });
        }
     }"
     class="fixed inset-0 z-50 overflow-y-auto">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="importModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="importModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="importModalOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-4xl w-full">
             
            <div class="bg-white px-8 pt-8 pb-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <h3 class="text-xl font-black text-secondary uppercase tracking-widest">Vincular do Banco</h3>
                    <button @click="importModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
                </div>

                <div class="mb-6">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" 
                               x-model="search" 
                               placeholder="Pesquisar por enunciado ou tag (ex: MATEMÁTICA)..." 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-all">
                    </div>
                </div>

                <form action="{{ route(auth()->user()->role . '.activities.questions.attach', $activity) }}" method="POST">
                    @csrf
                    <div class="max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 w-10"></th>
                                    <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Questão / Tags</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tipo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="q in filteredQuestions" :key="q.id">
                                    <tr class="hover:bg-blue-50/50 cursor-pointer transition" @click="document.getElementById('q_' + q.id).click()">
                                        <td class="px-4 py-4 text-center">
                                            <input type="checkbox" :id="'q_' + q.id" name="question_ids[]" :value="q.id" class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded cursor-pointer" @click.stop>
                                        </td>
                                        <td class="px-4 py-4 text-xs font-black text-gray-300" x-text="'#' + q.id"></td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-bold text-secondary line-clamp-1" x-text="q.statement.replace(/<[^>]*>?/gm, '')"></div>
                                            
                                            <div class="flex flex-wrap gap-1 mt-1.5" x-show="q.tags && q.tags.length > 0">
                                                <template x-for="tag in q.tags">
                                                    <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded border border-gray-200 text-[8px] font-black uppercase tracking-tighter" x-text="tag"></span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span :class="q.type === 'multiple_choice' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'" 
                                                  class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest"
                                                  x-text="q.type === 'multiple_choice' ? 'Múltipla' : 'Descritiva'">
                                            </span>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="filteredQuestions.length === 0">
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-gray-400 font-bold text-sm">
                                            Nenhuma questão encontrada para "<span x-text="search"></span>".
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="importModalOpen = false" class="px-6 py-2 text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">Cancelar</button>
                        <x-primary-button type="submit" class="px-10 py-3 shadow-lg shadow-primary/20">
                            Vincular Selecionadas
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div x-show="deadlineModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="deadlineModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="deadlineModalOpen = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="deadlineModalOpen" 
             x-transition:enter="ease-out duration-300" 
             class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full">
            
            <form :action="deadlineAction" method="POST">
                @csrf @method('PATCH')
                <div class="bg-white px-8 pt-8 pb-6">
                    <h3 class="text-lg font-black text-secondary uppercase tracking-widest mb-2">Prorrogar Prazo</h3>
                    <p class="text-sm text-gray-500 mb-6">Defina um prazo exclusivo para o aluno: <span class="font-bold text-primary" x-text="selectedStudent.name"></span></p>

                    <div>
                        <x-input-label for="custom_deadline" value="Nova Data e Hora Limite" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                        <input type="datetime-local" 
                               name="custom_deadline" 
                               id="custom_deadline" 
                               x-model="selectedStudent.currentDeadline"
                               class="block w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm font-bold text-secondary">
                        <p class="text-[10px] text-gray-400 mt-2 uppercase font-bold tracking-widest">Deixe vazio para voltar ao prazo padrão da turma.</p>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-4 flex justify-end gap-3">
                    <button type="button" @click="deadlineModalOpen = false" class="text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">Cancelar</button>
                    <x-primary-button type="submit">Salvar Novo Prazo</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</x-app-layout>