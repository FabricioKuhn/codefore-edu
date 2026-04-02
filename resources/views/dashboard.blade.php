<x-app-layout>
    <x-slot name="header">
        
        <h2 class="font-semibold text-xl text-[#333333] leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->role === 'student')
                <h3 class="text-2xl font-bold text-[#333333] mb-6">Área do Aluno - Minhas Turmas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($user->classrooms as $classroom)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition">
                            <div class="p-6">
                                <h4 class="text-xl font-bold text-[#333333] mb-2">{{ $classroom->name }}</h4>
                                <p class="text-gray-500 mb-4">{{ $classroom->subject }}</p>
                                <a href="{{ route('student.classrooms.show', $classroom) }}" class="inline-flex items-center px-4 py-2 bg-[#00ad9a] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008f7f] transition">
                                    Acessar Turma
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white p-6 shadow-sm sm:rounded-lg border border-gray-100 text-center text-gray-500">
                            Nenhuma turma matriculada ainda.
                        </div>
                    @endforelse
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 text-[#333333] font-semibold">
                        Bem-vindo ao Painel CodeForce, Professor!
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
