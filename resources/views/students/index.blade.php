<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')], ['name' => 'Secretaria de Alunos', 'url' => route(auth()->user()->role . '.students.index')]]" />
        <h2 class="font-semibold text-xl text-secondary leading-tight">
            {{ __('Gestão de Alunos') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ enrollModalOpen: false, selectedStudentId: null, selectedStudentName: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route(auth()->user()->role . '.students.index') }}" class="w-full md:w-1/3 flex">
                    <x-text-input name="search" value="{{ request('search') }}" placeholder="Buscar por nome, CPF ou e-mail..." class="w-full rounded-r-none" />
                    <button type="submit" class="px-4 py-2 bg-gray-200 border border-gray-300 rounded-r-md text-secondary hover:bg-gray-300 font-semibold transition">
                        Filtrar
                    </button>
                </form>

                <a href="{{ route(auth()->user()->role . '.students.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest  transition">
                    + Cadastrar Novo Aluno
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-secondary uppercase bg-gray-50 border-b">
                            <tr>
                                <th scope="col" class="px-6 py-3">Matrícula (ID)</th>
                                <th scope="col" class="px-6 py-3">Nome / E-mail</th>
                                <th scope="col" class="px-6 py-3">Telefone</th>
                                <th scope="col" class="px-6 py-3">Turmas</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-900">#{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-secondary">{{ $student->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">{{ $student->phone ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($student->classrooms as $classroom)
                                                <span class="bg-primary text-white text-[10px] font-bold px-2 py-1 rounded">{{ $classroom->name }}</span>
                                            @empty
                                                <span class="text-xs text-gray-400">Sem turma</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($student->is_active)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-400">Ativo</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">Inativo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap flex justify-end">
                                        
                                        <button @click="enrollModalOpen = true; selectedStudentId = {{ $student->id }}; selectedStudentName = '{{ $student->name }}'" class="inline-block transition" data-tooltip="Vincular à Turma">
                                            <svg class="w-5 h-5 text-purple-600 hover:text-purple-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        </button>

                                        <a href="{{ route(auth()->user()->role . '.students.edit', $student) }}" class="inline-block transition" data-tooltip="Editar Aluno">
                                            <svg class="w-5 h-5 text-amber-500 hover:text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <form action="{{ route(auth()->user()->role . '.students.toggle-status', $student) }}" method="POST" class="inline" onsubmit="return confirm('Mudar o status deste aluno?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-block transition" data-tooltip="{{ $student->is_active ? 'Inativar Aluno' : 'Ativar Aluno' }}">
                                                @if($student->is_active)
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
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Nenhum aluno encontrado na secretaria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $students->links() }}
                </div>
            </div>

            <div x-show="enrollModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-transition>
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative" @click.away="enrollModalOpen = false">
                    <button @click="enrollModalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
                    
                    <h3 class="text-lg font-bold text-secondary mb-4">Vincular Aluno à Turma</h3>
                    <p class="text-sm text-gray-600 mb-4">Selecione a turma para o aluno: <span x-text="selectedStudentName" class="font-semibold text-primary"></span></p>
                    
                    <form action="{{ route(auth()->user()->role . '.students.enroll', $student->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="classroom_id" value="Turma" />
                            <select name="classroom_id" id="classroom_id" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm w-full mt-1" required>
                                <option value="">-- Selecione uma Turma --</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}">{{ $classroom->name }} ({{ $classroom->subject }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="enrollModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Cancelar</button>
                            <x-primary-button>Confirmar Vínculo</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>