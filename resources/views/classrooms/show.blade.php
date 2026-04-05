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

    <div class="py-12" x-data="{ activeTab: 'alunos', showAttendanceModal: false, showCancelModal: false, showRegisterModal: false, selectedLesson: {} }">
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
                                    title: '{{ $lesson->title ?? 'Aula Agendada' }}',
                                    status: '{{ $lesson->status }}',
                                    content: {{ json_encode($lesson->content ?? '') }},
                                    attendances: {{ $lesson->attendances->pluck('status', 'user_id')->toJson() }}
                                }; showRegisterModal = true" 
                                class="text-secondary font-black text-[10px] uppercase mr-3 hover:brightness-90 transition">Registro</button>
                                
                                <button @click="selectedLesson = { id: {{ $lesson->id }}, title: '{{ $lesson->title ?? 'Aula Agendada' }}' }; showCancelModal = true" class="text-red-400 font-black text-[10px] uppercase hover:brightness-90 transition">Cancelar</button>
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

            {{-- ABA: TAREFAS --}}
            <div x-show="activeTab === 'tarefas'" x-cloak class="bg-white rounded-b-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">ID</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Atividade</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">XP</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Prazos</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($classroom->activities as $activity)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-xs font-black text-gray-300">{{ $activity->id }}</td>
                            <td class="px-6 py-4 font-bold text-sm text-secondary">{{ $activity->title }}</td>
                            <td class="px-6 py-4 font-black text-xs text-primary">{{ $activity->base_xp }} XP</td>
                            <td class="px-6 py-4 text-[10px] font-bold text-gray-500 uppercase">
                                {{ $activity->start_date ? $activity->start_date->format('d/m/Y') : 'N/D' }} 
                                <br>até <span class="text-red-400">{{ $activity->end_date ? $activity->end_date->format('d/m/Y') : 'N/D' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-[9px] font-black uppercase {{ 
                                    $activity->status === 'draft' ? 'bg-gray-100 text-gray-500' : (
                                    $activity->status === 'active' || $activity->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')
                                }}">
                                    {{ $activity->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route(auth()->user()->role . '.activities.show', $activity) }}" class="text-primary font-black text-[10px] uppercase mr-3">Gerenciar</a>
                                <button class="text-red-400 font-black text-[10px] uppercase">Inativar</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        {{-- MODAL: FAZER CHAMADA --}}
        <div x-show="showAttendanceModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="showAttendanceModal = false"></div>
                <div class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all">
                    <form :action="'/lessons/' + selectedLesson.id + '/attendance'" method="POST">
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
                    <form :action="'/lessons/' + selectedLesson.id + '/cancel'" method="POST">
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

        {{-- MODAL: REGISTRO DE AULA --}}
        <div x-show="showRegisterModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="showRegisterModal = false"></div>
                <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
                    <form :action="'/lessons/' + selectedLesson.id + '/register'" method="POST">
                        @csrf
                        <div class="bg-secondary px-6 py-4">
                            <h3 class="text-lg font-black text-white uppercase tracking-tight" x-text="'Registro de Aula: ' + selectedLesson.title"></h3>
                        </div>
                        <div class="p-6">
                            <x-input-label value="Conteúdo Ministrado" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <textarea name="content" x-model="selectedLesson.content" required rows="6" class="block w-full border-gray-100 bg-gray-50 rounded-xl focus:border-secondary focus:ring-secondary shadow-sm text-sm" placeholder="Descreva o que foi trabalhado em aula..."></textarea>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                            <button type="button" @click="showRegisterModal = false" class="text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">Cancelar</button>
                            <x-primary-button>Salvar Registro</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL: MATRICULAR ALUNO --}}
        <div x-data="{ open: false }" @abrir-modal-aluno.window="open = true" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="open = false"></div>
                <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
                    <form action="{{ route(auth()->user()->role . '.classrooms.students.store', $classroom) }}" method="POST">
                        @csrf
                        <div class="bg-primary px-6 py-4 border-b border-white/10">
                            <h3 class="text-lg font-black text-secondary uppercase tracking-tight">Matricular Aluno</h3>
                        </div>
                        <div class="p-6 space-y-4 bg-white">
                            <div>
                                <x-input-label value="Nome Completo" class="text-[10px] font-black uppercase text-gray-400" />
                                <x-text-input name="name" class="block mt-1 w-full" :value="old('name')" required />
                            </div>
                            <div>
                                <x-input-label value="E-mail do Aluno" class="text-[10px] font-black uppercase text-gray-400" />
                                <x-text-input type="email" name="email" class="block mt-1 w-full" :value="old('email')" required />
                            </div>
                            <div>
                                <x-input-label value="Senha Provisória" class="text-[10px] font-black uppercase text-gray-400" />
                                <x-text-input type="password" name="password" class="block mt-1 w-full" required />
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                            <button type="button" @click="open = false" class="text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">Cancelar</button>
                            <x-primary-button>Confirmar Matrícula</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>