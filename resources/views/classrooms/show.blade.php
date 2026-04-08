<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data>
            <h2 class="text-xl font-bold text-secondary leading-tight">
                {{ $classroom->name }} | <span class="text-gray-400 font-normal">{{ $classroom->subject }}</span>
            </h2>
            <div class="flex gap-2">
                 <x-primary-button @click="$dispatch('abrir-modal-aluno')">Matricular Aluno</x-primary-button>
                 <a href="{{ route(auth()->user()->role . '.activities.create', ['classroom_id' => $classroom->id]) }}">
                    <x-primary-button>Nova Atividade</x-primary-button>
                 </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
    activeTab: 'alunos', 
    showAttendanceModal: false, 
    showCancelModal: false, 
    showRegisterModal: false, 
    enrollModalOpen: false,
    selectedLesson: {},
    {{-- A mágica está aqui: identifica se usa /admin ou /professor --}}
    urlPrefix: '{{ auth()->user()->role === 'teacher' ? 'professor' : 'admin' }}' 
}" 
@abrir-modal-aluno.window="enrollModalOpen = true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Menu de Abas --}}
            <div class="flex border-b border-gray-200 mb-6 bg-white rounded-t-lg px-4">
                <button @click="activeTab = 'alunos'" :class="activeTab === 'alunos' ? 'border-primary text-primary' : 'border-transparent text-gray-400'" class="px-6 py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition">Alunos</button>
                <button @click="activeTab = 'aulas'" :class="activeTab === 'aulas' ? 'border-primary text-primary' : 'border-transparent text-gray-400'" class="px-6 py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition">Aulas</button>
                <button @click="activeTab = 'tarefas'" :class="activeTab === 'tarefas' ? 'border-primary text-primary' : 'border-transparent text-gray-400'" class="px-6 py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition">Tarefas / Atividades</button>
            </div>

            {{-- ABA: ALUNOS --}}
            <div x-show="activeTab === 'alunos'" class="bg-white rounded-b-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Nome do Aluno</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Aulas (R/P)</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Frequência</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">XP Acumulada</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $concludedLessonsCount = $classroom->lessons->where('status', 'recorded')->count();
                            $totalLessonsCount = $classroom->lessons->count();
                        @endphp
                        @foreach($classroom->students as $student)
                        @php
                            $presentCount = $classroom->lessons->map(function($l) use ($student) {
                                return $l->attendances->where('user_id', $student->id)->where('status', 'present')->first();
                            })->filter()->count();

                            $frequency = $concludedLessonsCount > 0 ? round(($presentCount / $concludedLessonsCount) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-bold text-sm text-secondary">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-500">{{ $presentCount }} / {{ $totalLessonsCount }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="bg-primary h-full transition-all duration-500" style="width: {{ $frequency }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-black text-secondary whitespace-nowrap">{{ $frequency }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-black text-xs text-primary">0 XP</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-[9px] font-black uppercase">Ativo</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-primary font-black text-[10px] uppercase mr-3">Ver Dados</button>
                                <button class="text-red-400 font-black text-[10px] uppercase">Inativar</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ABA: AULAS --}}
            <div x-show="activeTab === 'aulas'" x-cloak class="bg-white rounded-b-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">#</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Aula</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Data/Hora</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Presenças</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($classroom->lessons as $lesson)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-xs font-black text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-bold text-sm text-secondary">{{ $lesson->title ?? 'Aula Agendada' }}</td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-secondary">{{ $lesson->date->format('d/m/Y') }}</p>
                                <p class="text-[9px] font-black text-gray-400 uppercase">
                                    {{ \Carbon\Carbon::parse($lesson->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($lesson->end_time)->format('H:i') }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-[9px] font-black uppercase {{ 
                                    $lesson->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : (
                                    $lesson->status === 'canceled' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700')
                                }}">
                                    {{ $lesson->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-bold text-secondary">
                                    {{ $lesson->attendances->where('status', 'present')->count() }} / {{ $classroom->students->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($lesson->status !== 'canceled')
                                <button @click="selectedLesson = { 
                                    id: {{ $lesson->id }}, 
                                    title: '{{ $lesson->title ?? 'Aula Agendada' }}',
                                    status: '{{ $lesson->status }}',
                                    content: {{ json_encode($lesson->content ?? '') }},
                                    attendances: {{ $lesson->attendances->pluck('status', 'user_id')->toJson() }}
                                }; showAttendanceModal = true" 
                                class="text-primary font-black text-[10px] uppercase mr-3 hover:brightness-90 transition">
                                    <span x-text="selectedLesson.id === {{ $lesson->id }} && selectedLesson.status === 'recorded' ? 'Alterar Chamada' : ({{ $lesson->status === 'recorded' ? 'true' : 'false' }} ? 'Alterar Chamada' : 'Fazer Chamada')"></span>
                                </button>
                                
                                <button @click="selectedLesson = { 
    id: {{ $lesson->id }}, 
    title: {{ json_encode($lesson->title ?? '') }},
    status: '{{ $lesson->status }}',
    content: {{ json_encode($lesson->content ?? '') }},
    video_url: {{ json_encode($lesson->video_url ?? '') }},
    xp_reward: {{ $lesson->xp_reward ?? 0 }},
    has_material: {{ $lesson->main_material_path ? 'true' : 'false' }},
    attendances: {{ $lesson->attendances->pluck('status', 'user_id')->toJson() }},
    {{-- 🌟 NOVO: Lista de IDs das atividades já vinculadas --}}
    linked_activities: {{ $lesson->activities->pluck('id')->toJson() }}
}; showRegisterModal = true" 
class="text-secondary font-black text-[10px] uppercase mr-3 hover:brightness-90 transition">
    Configurar Aula
</button>
                                @endif
                                
                                @if($lesson->status === 'scheduled')
        <button @click="selectedLesson = { id: {{ $lesson->id }}, title: '{{ $lesson->title ?? 'Aula Agendada' }}' }; showCancelModal = true" 
        class="text-red-400 font-black text-[10px] uppercase hover:brightness-90 transition">
            Cancelar
        </button>
    @else
        {{-- Opcional: Mostrar um aviso pequeno se a aula estiver cancelada --}}
        @if($lesson->status === 'canceled')
            <span class="text-[9px] font-black text-gray-300 uppercase italic">Aula Cancelada</span>
        @endif
    @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-gray-400 font-bold text-sm">As aulas ainda não foram geradas para esta turma.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ABA: TAREFAS E AVALIAÇÕES --}}
            <div x-show="activeTab === 'tarefas'" x-cloak class="bg-white rounded-b-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">ID</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Atividade</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Entregas pendentes</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($classroom->activities as $activity)
                        @php
                            // Lógica para contar alunos aguardando correção nesta atividade
                            $pendingSubmissions = $activity->submissions()->where('status', 'waiting_evaluation')->count();
                            $hasPending = $pendingSubmissions > 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-xs font-black text-gray-300">{{ $activity->id }}</td>
                            
                            {{-- Nome da Atividade e Detalhes --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-sm text-secondary block">{{ $activity->title }}</span>
                                <span class="text-[9px] font-black text-primary uppercase">{{ $activity->base_xp }} XP</span>
                                <span class="text-gray-300 mx-1">•</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase">
                                    Vence em: {{ $activity->end_date ? $activity->end_date->format('d/m/Y') : 'S/ Prazo' }}
                                </span>
                            </td>
                            
                            {{-- Coluna de Correções Pendentes --}}
                            <td class="px-6 py-4 text-center">
                                @if($hasPending)
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg text-xs font-bold animate-pulse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $pendingSubmissions }} para corrigir
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-gray-300">-</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-[9px] font-black uppercase {{ 
                                    $activity->status === 'draft' ? 'bg-gray-100 text-gray-500' : (
                                    $activity->status === 'active' || $activity->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')
                                }}">
                                    {{ $activity->status_label }}
                                </span>
                            </td>
                            
                            {{-- Ações --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    {{-- Botão de Correção (Destaque se tiver pendência) --}}
                                    @if($activity->status !== 'draft')
                                        <a href="{{ route(auth()->user()->role . '.submissions.index', $activity) }}" 
                                           class="text-[10px] font-black uppercase transition-all px-3 py-1.5 rounded-lg {{ $hasPending ? 'bg-yellow-400 text-yellow-900 hover:brightness-110 shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                            Correções
                                        </a>
                                    @endif
                                    
                                    {{-- Botão Gerenciar --}}
                                    <a href="{{ route(auth()->user()->role . '.activities.show', $activity) }}" class="text-primary font-black text-[10px] uppercase hover:underline">
                                        Configurar
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($classroom->activities->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-bold text-sm">
                                Nenhuma atividade vinculada a esta turma.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>

        {{-- MODAL: FAZER CHAMADA --}}
        <div x-show="showAttendanceModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="showAttendanceModal = false"></div>
                <div class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all">
                    <form :action="'/' + urlPrefix + '/lessons/' + selectedLesson.id + '/attendance'" method="POST">
                        @csrf
                        <div class="bg-primary px-6 py-4">
                            <h3 class="text-lg font-black text-secondary uppercase tracking-tight" x-text="'Chamada: ' + selectedLesson.title"></h3>
                        </div>
                        <div class="p-6 max-h-[60vh] overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase">Aluno</th>
                                        <th class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase text-center">Presença / Falta</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($classroom->students as $student)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 text-sm font-bold text-secondary">{{ $student->name }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-6">
                                                <label class="flex items-center cursor-pointer group">
                                                    <input type="radio" name="attendance[{{ $student->id }}][status]" value="present" 
                                                        :checked="selectedLesson.attendances && selectedLesson.attendances[{{ $student->id }}] === 'present' || (!selectedLesson.attendances || !selectedLesson.attendances[{{ $student->id }}])" 
                                                        class="text-primary focus:ring-primary">
                                                    <span class="ml-2 text-[9px] font-black text-gray-400 group-hover:text-primary transition uppercase">Presente</span>
                                                </label>
                                                <label class="flex items-center cursor-pointer group">
                                                    <input type="radio" name="attendance[{{ $student->id }}][status]" value="justified" 
                                                        :checked="selectedLesson.attendances && selectedLesson.attendances[{{ $student->id }}] === 'justified'"
                                                        class="text-secondary focus:ring-secondary">
                                                    <span class="ml-2 text-[9px] font-black text-gray-400 group-hover:text-secondary transition uppercase">Justif.</span>
                                                </label>
                                                <label class="flex items-center cursor-pointer group">
                                                    <input type="radio" name="attendance[{{ $student->id }}][status]" value="absent" 
                                                        :checked="selectedLesson.attendances && selectedLesson.attendances[{{ $student->id }}] === 'absent'"
                                                        class="text-red-400 focus:ring-red-400">
                                                    <span class="ml-2 text-[9px] font-black text-gray-400 group-hover:text-red-400 transition uppercase">Falta</span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                            <button type="button" @click="showAttendanceModal = false" class="text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">Cancelar</button>
                            <x-primary-button>Salvar Chamada</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL: CANCELAR AULA --}}
        <div x-show="showCancelModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="showCancelModal = false"></div>
                <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
                    <form :action="'/' + urlPrefix + '/lessons/' + selectedLesson.id + '/cancel'" method="POST">
                        @csrf
                        <div class="bg-red-500 px-6 py-4">
                            <h3 class="text-lg font-black text-white uppercase tracking-tight" x-text="'Cancelar Aula: ' + selectedLesson.title"></h3>
                        </div>
                        <div class="p-6">
                            <x-input-label value="Justificativa do Cancelamento" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <textarea name="justification" required rows="4" class="block w-full border-gray-100 bg-gray-50 rounded-xl focus:border-red-400 focus:ring-red-400 shadow-sm text-sm" placeholder="Descreva o motivo do cancelamento..."></textarea>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                            <button type="button" @click="showCancelModal = false" class="text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">Voltar</button>
                            <button type="submit" class="bg-red-500 hover:brightness-90 text-white font-black text-[10px] uppercase tracking-widest px-6 py-3 rounded-lg transition-all">Confirmar Cancelamento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL: CONFIGURAR E REGISTRAR AULA (LMS) --}}
        <div x-show="showRegisterModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="showRegisterModal = false"></div>
                <div class="relative bg-white rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden transform transition-all">
                    
                    <form :action="'/' + urlPrefix + '/lessons/' + selectedLesson.id + '/register'" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-secondary px-6 py-4">
                            <h3 class="text-lg font-black text-white uppercase tracking-tight" x-text="(selectedLesson.title ? 'Configurar: ' + selectedLesson.title : 'Configurar Nova Aula')"></h3>
                        </div>
                        
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[70vh] overflow-y-auto">
                            
                            <div class="space-y-4">
                                <div>
                                    <x-input-label value="Título da Aula *" class="text-[10px] font-black uppercase text-gray-400 mb-1" />
                                    <x-text-input name="title" x-model="selectedLesson.title" required class="w-full text-sm" placeholder="Ex: Aula 01 - Introdução" />
                                </div>

                                <div>
                                    <x-input-label value="Resumo / Registro da Aula *" class="text-[10px] font-black uppercase text-gray-400 mb-1" />
                                    <textarea name="content" x-model="selectedLesson.content" required rows="5" class="block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm" placeholder="Descreva o conteúdo ministrado..."></textarea>
                                </div>
                            </div>

                            <div class="space-y-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                
                                <div>
                                    <x-input-label value="Link do Vídeo (YouTube/Vimeo)" class="text-[10px] font-black uppercase text-gray-400 mb-1" />
                                    <x-text-input name="video_url" type="url" x-model="selectedLesson.video_url" class="w-full text-sm" placeholder="https://..." />
                                </div>

                                <div>
                                    <x-input-label value="Material Principal (PDF, PPTX)" class="text-[10px] font-black uppercase text-gray-400 mb-1" />
                                    <input type="file" name="material" accept=".pdf,.doc,.docx,.ppt,.pptx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition cursor-pointer"/>
                                    <p x-show="selectedLesson.has_material" class="text-[10px] text-green-600 font-bold mt-1">✓ Material já anexado. Enviar novo substituirá o atual.</p>
                                </div>

                                <div class="pt-2 border-t border-gray-200">
                                    <x-input-label value="Recompensa (XP)" class="text-[10px] font-black uppercase text-gray-400 mb-1" />
                                    <div class="flex items-center gap-2">
                                        <x-text-input name="xp_reward" type="number" min="0" x-model="selectedLesson.xp_reward" class="w-24 text-sm font-bold text-primary" />
                                        <span class="text-xs font-bold text-gray-400">XP para o aluno ao concluir</span>
                                    </div>
                                </div>

                            </div>
                        
                        <div class="md:col-span-2 pt-4 border-t border-gray-200">
                                <x-input-label value="Vincular Tarefas a esta Aula" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    @forelse($classroom->activities as $activity)
                                        <label class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-primary/50 transition">
                                            <input type="checkbox" name="activity_ids[]" value="{{ $activity->id }}" 
                                                   x-model="selectedLesson.linked_activities" 
                                                   class="rounded border-gray-300 text-primary focus:ring-primary shadow-sm w-4 h-4">
                                            
                                            <div class="flex flex-col flex-1 truncate">
                                                <span class="text-xs font-bold text-secondary truncate">{{ $activity->title }}</span>
                                                <span class="text-[9px] text-gray-400 uppercase font-black">{{ $activity->status_label }}</span>
                                            </div>
                                            
                                            <span class="text-[10px] font-black bg-primary/10 text-primary px-2 py-1 rounded-md">{{ $activity->base_xp }} XP</span>
                                        </label>
                                    @empty
                                        <p class="text-xs text-gray-400 italic col-span-2 text-center py-2">Nenhuma tarefa criada nesta turma ainda. Você pode criar uma na aba "Tarefas".</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                            <button type="button" @click="showRegisterModal = false" class="text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">Cancelar</button>
                            <x-primary-button>Salvar e Configurar Aula</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL: MATRICULAR ALUNO --}}
        <div x-show="enrollModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-secondary/40 backdrop-blur-sm transition-opacity"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-secondary">Vincular Aluno à Turma</h3>
                <button @click="enrollModalOpen = false" class="text-gray-400 hover:text-secondary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <form action="{{ route(auth()->user()->role . '.classrooms.students.store', $classroom) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <x-input-label for="student_id" value="Selecione o Aluno" />
                        <select name="student_id" id="student_id" required class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm">
                            <option value="">Escolha um aluno da lista...</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500">Apenas alunos cadastrados na instituição e que ainda não estão nesta turma aparecem aqui.</p>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" @click="enrollModalOpen = false" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-50 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-xl font-bold hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                        Confirmar Vínculo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
        
    </div>
</x-app-layout>