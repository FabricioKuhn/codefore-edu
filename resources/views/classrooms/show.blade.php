<x-app-layout>
    <x-slot name="header">  
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route('dashboard')],
            ['name' => 'Minhas Turmas', 'url' => route('classrooms.index')],
            ['name' => $classroom->name, 'url' => route('classrooms.show', $classroom)]
        ]" />
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-[#333333] leading-tight">
                {{ $classroom->name }} - {{ $classroom->subject }}
            </h2>
            <a href="{{ route('classrooms.activities.create', $classroom) }}">
                <x-primary-button type="button">Nova Missão</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Alertas de Sucesso/Erro --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-emerald-500 text-emerald-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                    <span class="block sm:inline font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                    <span class="block sm:inline font-bold">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Card de Informações da Turma --}}
            <div class="mb-6 bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-[#333333]">{{ $classroom->name }}</h3>
                    <p class="text-gray-500">{{ $classroom->subject }}</p>
                </div>
                <div class="mt-4 md:mt-0 text-center bg-gray-100 p-4 rounded-lg border border-gray-200">
                    <span class="text-sm uppercase text-gray-500 font-semibold tracking-wider">Código de Convite</span>
                    <div class="text-3xl font-mono font-bold tracking-widest mt-1 text-[#00ad9a]">{{ $classroom->join_code }}</div>
                </div>
            </div>

            {{-- Seção de Missões --}}
            <h3 class="text-xl font-bold text-[#333333] mb-4 px-2">Missões / Avaliações</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($classroom->activities as $activity)
                    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 border-t-4 border-t-[#00ad9a] p-6 hover:shadow-md transition">
                        <h4 class="text-lg font-bold mb-2 text-[#333333]">{{ $activity->title }}</h4>
                        
                        <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                            <span class="font-medium text-[#00ad9a]">XP Base: {{ $activity->base_xp }}</span>
                            <span class="px-2 py-1 bg-gray-100 rounded-md text-[10px] font-bold uppercase">{{ $activity->status }}</span>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                            <a href="{{ route('activities.show', $activity) }}" class="text-[#00ad9a] hover:underline font-semibold text-sm">Gerenciar Missão &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white shadow-sm sm:rounded-lg p-10 text-center text-gray-400 border border-dashed border-gray-300">
                        Nenhuma missão cadastrada nesta turma ainda.
                    </div>
                @endforelse
            </div>

            {{-- Seção de Alunos --}}
            <div class="mt-12 flex items-center justify-between mb-4 px-2">
                <h3 class="text-xl font-bold text-[#333333]">Alunos Matriculados</h3>
                {{-- Gatilho do Modal via Alpine --}}
                <x-primary-button type="button" @click="$dispatch('abrir-modal-aluno')">
                    Cadastrar Aluno
                </x-primary-button>
            </div>
            
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 overflow-hidden">
                @if($classroom->students->count() > 0)
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border-b border-gray-100 py-3 px-6 text-xs font-black text-gray-400 uppercase">Nome</th>
                                <th class="border-b border-gray-100 py-3 px-6 text-xs font-black text-gray-400 uppercase">Email</th>
                                <th class="border-b border-gray-100 py-3 px-6 text-xs font-black text-gray-400 uppercase text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($classroom->students as $student)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 text-[#333333] font-bold text-sm">{{ $student->name }}</td>
                                    <td class="py-4 px-6 text-gray-500 text-sm">{{ $student->email }}</td>
                                    <td class="py-4 px-6 text-right">
                                        <button class="text-gray-300 hover:text-red-500 transition">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-gray-400 py-10">
                        Nenhum aluno matriculado nesta turma ainda.
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL DE MATRÍCULA (Alpine.js) --}}
        <div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" 
             @abrir-modal-aluno.window="open = true" 
             x-cloak>
            
            {{-- Fundo e Container --}}
            <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    
                    {{-- Backdrop --}}
                    <div x-show="open" 
                         x-transition.opacity 
                         class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" 
                         @click="open = false"></div>

                    {{-- Card do Modal --}}
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        
                        <form action="{{ route('classrooms.students.store', $classroom) }}" method="POST">
                            @csrf
                            <div class="bg-primary px-6 py-6">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-lg font-black text-secondary uppercase tracking-tight">Matricular Aluno</h3>
                                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label value="Nome Completo" class="text-[10px] font-black uppercase text-gray-400" />
                                        <x-text-input name="name" class="block mt-1 w-full" :value="old('name')" required />
                                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                    </div>

                                    <div>
                                        <x-input-label value="E-mail do Aluno" class="text-[10px] font-black uppercase text-gray-400" />
                                        <x-text-input type="email" name="email" class="block mt-1 w-full" :value="old('email')" required />
                                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                    </div>

                                    <div>
                                        <x-input-label value="Senha Provisória" class="text-[10px] font-black uppercase text-gray-400" />
                                        <x-text-input type="password" name="password" class="block mt-1 w-full" required />
                                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                <x-primary-button>Confirmar Matrícula</x-primary-button>
                                <button type="button" @click="open = false" class="text-xs font-bold text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>