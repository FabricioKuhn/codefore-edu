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
                                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                                        
                                        <button @click="enrollModalOpen = true; selectedStudentId = {{ $student->id }}; selectedStudentName = '{{ $student->name }}'" class="text-blue-600 hover:text-blue-900 font-semibold" title="Vincular à Turma">
                                            Vincular
                                        </button>

                                        <a href="{{ route(auth()->user()->role . '.students.edit', $student) }}" class="text-gray-600 hover:text-primary font-semibold" title="Editar Cadastro">
                                            Editar
                                        </a>

                                        <form action="{{ route(auth()->user()->role . '.students.toggle-status', $student) }}" method="POST" class="inline" onsubmit="return confirm('Mudar o status deste aluno?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="{{ $student->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }} font-semibold">
                                                {{ $student->is_active ? 'Inativar' : 'Ativar' }}
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
                    
                    <form :action="`/students/${selectedStudentId}/enroll`" method="POST">
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