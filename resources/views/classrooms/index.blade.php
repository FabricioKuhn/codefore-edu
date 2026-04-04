<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route('dashboard')],
            ['name' => 'Minhas Turmas', 'url' => route('classrooms.index')]
        ]" />
        <h2 class="font-semibold text-xl text-secondary leading-tight">
            {{ __('Gestão de Turmas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('classrooms.index') }}" class="w-full md:w-1/3 flex">
                    <x-text-input name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou matéria..." class="w-full rounded-r-none border-r-0 focus:ring-0" />
                    <button type="submit" class="px-4 py-2 bg-gray-200 border border-gray-300 rounded-r-md text-secondary hover:bg-gray-300 font-semibold transition">
                        Filtrar
                    </button>
                </form>

                <a href="{{ route('classrooms.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest  transition">
                    + Nova Turma
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-secondary uppercase bg-gray-50 border-b">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Nome da Turma</th>
                                <th scope="col" class="px-6 py-3">Matéria</th>
                                <th scope="col" class="px-6 py-3 text-center">Alunos</th>
                                <th scope="col" class="px-6 py-3 text-center">Aulas Previstas</th>
                                <th scope="col" class="px-6 py-3">Professor</th>
                                <th scope="col" class="px-6 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($classrooms as $classroom)
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-900">#{{ str_pad($classroom->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-secondary">{{ $classroom->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-primary text-white text-[10px] font-bold px-2 py-1 rounded">
                                            {{ $classroom->subject }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-semibold text-secondary">
                                        {{ $classroom->students_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-semibold text-secondary">
                                        {{ $classroom->total_lessons ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $classroom->teacher->name ?? 'Não Atribuído' }}
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-3">
                                        
                                        <a href="{{ route('classrooms.show', $classroom) }}" class="text-primary hover:text-[#009688] font-semibold" title="Ver Atividades e Gerenciar">
                                            Gerenciar
                                        </a>

                                        <a href="{{ route('classrooms.edit', $classroom) }}" class="text-gray-600 hover:text-primary font-semibold" title="Editar Turma">
                                            Editar
                                        </a>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Nenhuma turma encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($classrooms->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $classrooms->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>