<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-secondary leading-tight">
                Banco de Questões
            </h2>
            <a href="{{ route(auth()->user()->role . '.questions.create') }}">
                <x-primary-button>Nova Questão</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">ID</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Enunciado</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Tipo</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Peso Padrão</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($questions as $question)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-xs font-black text-gray-300">#{{ $question->id }}</td>
                            <td class="px-6 py-4">
    {{-- Enunciado Resumido --}}
    <div class="text-sm font-bold text-secondary line-clamp-2">
        {!! strip_tags($question->statement) !!}
    </div>

    {{-- Badges das Tags --}}
    @if($question->tags && count($question->tags) > 0)
        <div class="flex flex-wrap gap-1 mt-2">
            @foreach($question->tags as $tag)
                <span class="bg-gray-50 text-gray-400 px-1.5 py-0.5 rounded border border-gray-100 text-[8px] font-black uppercase tracking-tighter">
                    {{ $tag }}
                </span>
            @endforeach
        </div>
    @endif
</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-[9px] font-black uppercase {{ $question->type === 'multiple_choice' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $question->type === 'multiple_choice' ? 'Múltipla Escolha' : 'Descritiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-xs text-primary">
                                {{ $question->default_weight }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-[9px] font-black uppercase {{ $question->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $question->status ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route(auth()->user()->role . '.questions.edit', $question) }}" class="text-primary font-black text-[10px] uppercase mr-3 hover:underline">
    Editar
</a>
                                <form action="{{ route(auth()->user()->role . '.questions.update_status', $question) }}" method="POST" class="inline-block">
    @csrf 
    @method('PUT')
    
    @if($question->status)
        <button type="submit" class="text-red-400 font-black text-[10px] uppercase hover:underline" title="Ocultar questão de novas provas">
            Inativar
        </button>
    @else
        <button type="submit" class="text-green-500 font-black text-[10px] uppercase hover:underline" title="Disponibilizar questão novamente">
            Ativar
        </button>
    @endif
</form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-gray-400 font-bold text-sm">Nenhuma questão cadastrada no banco da instituição.</p>
                                <a href="{{ route(auth()->user()->role . '.questions.create') }}" class="text-primary hover:underline text-xs mt-2 inline-block">Criar primeira questão</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{-- Paginação --}}
                @if($questions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $questions->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>