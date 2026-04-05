<x-app-layout>
    <div x-data="{ 
        showModal: {{ session('open_modal') ? 'true' : 'false' }}, 
        editMode: false, 
        teacher: { id: '', name: '', email: '' },
        errors: {}
    }" 
    x-init="
        @if(session('edit_teacher_id'))
            @php $editTeacher = $teachers->firstWhere('id', session('edit_teacher_id')) @endphp
            @if($editTeacher)
                editMode = true;
                showModal = true;
                teacher = { id: '{{ $editTeacher->id }}', name: '{{ $editTeacher->name }}', email: '{{ $editTeacher->email }}' };
            @endif
        @endif
    "
    @open-teacher-modal.window="showModal = true; editMode = false; teacher = { id: '', name: '', email: '' }; errors = {}"
    @edit-teacher.window="showModal = true; editMode = true; teacher = $event.detail; errors = {}"
    @keyup.escape.window="showModal = false">

        <x-slot name="header">
            <x-breadcrumbs :links="[['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')], ['name' => 'Equipe de Professores', 'url' => route(auth()->user()->role . '.teachers.index')]]" />
            <div class="flex justify-between items-center">
                
                <h2 class="font-semibold text-xl text-secondary leading-tight">
                    {{ __('Equipe de Professores') }}
                </h2>
                <a href="{{ route(auth()->user()->role . '.teachers.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest  transition">
                    + Novo Professor
                </a>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Alertas -->
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="font-bold text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-xl flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-bold text-sm">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">Professor</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">E-mail</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider text-center">Turmas</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($teachers as $teacher)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center text-primary font-bold text-xs">
                                                {{ substr($teacher->name, 0, 1) }}
                                            </div>
                                            <span class="font-bold text-sm text-secondary">{{ $teacher->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $teacher->email }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                            {{ $teacher->taught_classrooms_count }} turmas
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $teacher->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $teacher->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap flex justify-end">
                                        <button @click="enrollModalOpen = true; selectedStudentId = {{ $teacher->id }}; selectedStudentName = '{{ $teacher->name }}'" class="inline-block transition" data-tooltip="Vincular à Turma">
                                            <svg class="w-5 h-5 text-purple-600 hover:text-purple-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                        </button>

                                        <a href="{{ route(auth()->user()->role . '.teachers.edit', $teacher) }}" class="inline-block transition" data-tooltip="Editar Professor">
                                            <svg class="w-5 h-5 text-amber-500 hover:text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <form action="{{ route(auth()->user()->role . '.teachers.toggle-status', $teacher) }}" method="POST" class="inline" onsubmit="return confirm('Mudar o status deste professor?');">
    @csrf
    @method('PATCH')
    <button type="submit" class="inline-block transition" data-tooltip="{{ $teacher->is_active ? 'Inativar Professor' : 'Ativar Professor' }}">
        @if($teacher->is_active)
            <svg class="w-5 h-5 text-red-600 hover:text-red-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.524a6 6 0 018.367 8.366L13.477 14.89M9.224 5.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" clip-rule="evenodd"></path></svg>
        @else
            <svg class="w-5 h-5 text-emerald-600 hover:text-emerald-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        @endif
    </button>
</form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">
                                        Nenhum professor cadastrado.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <template x-if="showModal">
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-secondary/40 backdrop-blur-sm">
                    <div @click.away="showModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-lg text-secondary" x-text="editMode ? 'Editar Professor' : 'Novo Professor'"></h3>
                            <button @click="showModal = false" class="text-gray-400 hover:text-secondary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form :action="editMode ? '{{ url('teachers') }}/' + teacher.id : '{{ route(auth()->user()->role . '.teachers.store') }}'" method="POST" class="p-6 space-y-4">
                            @csrf
                            <template x-if="editMode">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase mb-1">Nome Completo</label>
                                <input type="text" name="name" x-model="teacher.name" required
                                       class="w-full border-gray-200 rounded-lg focus:ring-primary focus:border-primary font-medium text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase mb-1">E-mail Corporativo</label>
                                <input type="email" name="email" x-model="teacher.email" @blur="teacher.email = teacher.email.trim()" required 
                                       class="w-full border-gray-200 rounded-lg focus:ring-primary focus:border-primary font-medium text-sm">
                            </div>

                            <div x-data="{ showPassword: false }">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-1" x-text="editMode ? 'Nova Senha (deixe em branco para manter)' : 'Senha de Acesso'"></label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" :required="!editMode"
                                           class="w-full border-gray-200 rounded-lg focus:ring-primary focus:border-primary font-medium text-sm pr-10">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-primary transition">
                                        <svg x-show="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" />
                                        </svg>
                                        <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div x-data="{ showPasswordConfirm: false }">
                                <label class="block text-xs font-black text-gray-400 uppercase mb-1">Confirme a Senha</label>
                                <div class="relative">
                                    <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" :required="!editMode"
                                           class="w-full border-gray-200 rounded-lg focus:ring-primary focus:border-primary font-medium text-sm pr-10">
                                    <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-primary transition">
                                        <svg x-show="!showPasswordConfirm" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" />
                                        </svg>
                                        <svg x-show="showPasswordConfirm" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="pt-4 flex gap-3">
                                <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg font-bold text-sm hover:bg-gray-50 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg font-bold text-sm hover:opacity-90 transition-all shadow-md">
                                    Salvar Professor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-app-layout>
