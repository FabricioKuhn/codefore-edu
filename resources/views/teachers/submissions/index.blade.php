<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-secondary leading-tight mt-2">
            Central de Correção: {{ $activity->title }}
        </h2>
    </x-slot>

    <div class="py-0 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- Cabeçalho de Informações --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8 flex justify-between items-center">
            <div>
                <span class="px-3 py-1 bg-primary/10 text-primary rounded-lg text-[10px] font-black uppercase tracking-widest mb-3 inline-block">
                    Turma: {{ $activity->classroom->name }}
                </span>
                <h1 class="text-2xl font-black text-secondary">{{ $activity->title }}</h1>
                <p class="text-gray-500 font-medium text-sm mt-1">Total de Envios: {{ $submissions->whereIn('status', ['waiting_evaluation', 'evaluated'])->count() }}</p>
            </div>
            
            <div class="text-center bg-gray-50 px-6 py-4 rounded-2xl border border-gray-100">
                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">XP Máximo</span>
                <span class="text-2xl font-black text-primary">{{ $activity->base_xp }}</span>
            </div>
        </div>

        {{-- Lista de Alunos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($submissions as $submission)
                @php
                    $isWaiting = $submission->status === 'waiting_evaluation';
                    $isEvaluated = $submission->status === 'evaluated';
                    $isInProgress = $submission->status === 'in_progress';
                @endphp

                <div class="bg-white rounded-3xl shadow-sm border {{ $isWaiting ? 'border-yellow-200' : ($isEvaluated ? 'border-green-200' : 'border-gray-100') }} p-6 flex flex-col relative overflow-hidden transition-all hover:-translate-y-1 hover:shadow-md">
                    
                    {{-- Faixa lateral de cor (Opcional, dá um charme) --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $isWaiting ? 'bg-yellow-400' : ($isEvaluated ? 'bg-green-500' : 'bg-gray-200') }}"></div>

                    <div class="flex justify-between items-start mb-4 pl-2">
                        <h3 class="font-bold text-secondary text-lg truncate pr-4">{{ $submission->student->name }}</h3>
                        
                        @if($isWaiting)
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-[9px] font-black uppercase tracking-widest shrink-0 animate-pulse">Avaliar</span>
                        @elseif($isEvaluated)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-[9px] font-black uppercase tracking-widest shrink-0">Corrigido</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-md text-[9px] font-black uppercase tracking-widest shrink-0">Fazendo...</span>
                        @endif
                    </div>

                    <div class="space-y-2 mb-6 pl-2">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-gray-400 uppercase">Enviado em:</span>
                            <span class="font-medium text-secondary">{{ $submission->finished_at ? $submission->finished_at->format('d/m/Y H:i') : '--' }}</span>
                        </div>
                        @if($isEvaluated)
                            <div class="flex justify-between text-xs border-t border-gray-50 pt-2">
                                <span class="font-bold text-gray-400 uppercase">XP Obtido:</span>
                                <span class="font-black text-primary">{{ $submission->earned_xp }} / {{ $activity->base_xp }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Botão de Ação --}}
                    <div class="mt-auto pl-2">
                        @if($isWaiting || $isEvaluated)
                            <a href="{{ route('teacher.submissions.show', [$activity, $submission]) }}" class="block w-full text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all {{ $isWaiting ? 'bg-yellow-400 text-yellow-900 hover:brightness-110 shadow-lg shadow-yellow-400/30' : 'bg-gray-50 text-secondary hover:bg-gray-100 border border-gray-200' }}">
                                {{ $isWaiting ? 'Corrigir Agora' : 'Revisar Correção' }}
                            </a>
                        @else
                            <button disabled class="block w-full text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest bg-gray-50 text-gray-300 cursor-not-allowed">
                                Aguardando Aluno
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-gray-400 font-bold uppercase text-sm tracking-widest">Nenhum aluno iniciou esta atividade ainda.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>