<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route('student.dashboard')],
            ['name' => 'Minhas Turmas', 'url' => route('student.dashboard')],
            ['name' => $classroom->name, 'url' => '#']
        ]" />
        <h2 class="text-xl font-bold text-secondary leading-tight mt-2">
            Turma: {{ $classroom->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10 bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-8 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary text-2xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-secondary">{{ $classroom->name }}</h3>
                            <p class="text-gray-500 font-medium">{{ $classroom->subject }} • Prof. {{ $classroom->teacher->name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="bg-gray-50 px-6 py-3 rounded-xl border border-gray-100 text-center">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Seu Progresso</span>
                            <div class="text-xl font-black text-secondary">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="text-lg font-black text-secondary mb-6 px-2 uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-6 bg-primary rounded-full"></span>
                Missões Disponíveis
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($classroom->activities->where('status', 'active') as $activity)
                    @php
                        // Buscamos o status desse aluno especificamente nesta atividade
                        $submission = $activity->submissions->where('student_id', auth()->id())->first();
                        $status = $submission ? $submission->status : 'pending';
                        $isEnabled = $submission ? $submission->is_enabled : true;
                        
                        // Prazo individual ou geral
                        $deadline = $submission && $submission->custom_deadline ? $submission->custom_deadline : $activity->end_date;
                        $isExpired = $deadline && $deadline->isPast();
                    @endphp

                    @if($isEnabled)
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
    
    <div class="px-6 pt-6 flex justify-between items-start">
        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $activity->type === 'exam' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-blue-100 text-blue-700 border border-blue-200' }}">
            {{ $activity->type === 'exam' ? 'Prova' : 'Tarefa' }}
        </span>

        <span class="text-[9px] font-black uppercase px-2 py-1 rounded-md tracking-tighter
            {{ $status === 'pending' ? 'bg-gray-100 text-gray-400' : '' }}
            {{ $status === 'in_progress' ? 'bg-blue-50 text-blue-600 animate-pulse' : '' }}
            {{ $status === 'waiting_evaluation' ? 'bg-yellow-50 text-yellow-600' : '' }}
            {{ $status === 'evaluated' ? 'bg-green-50 text-green-600' : '' }}">
            {{ $status === 'pending' ? 'Pendente' : '' }}
            {{ $status === 'in_progress' ? 'Em Andamento' : '' }}
            {{ $status === 'waiting_evaluation' ? 'Avaliar' : '' }}
            {{ $status === 'evaluated' ? 'Concluída' : '' }}
        </span>
    </div>

    <div class="px-7 pb-6 pt-4 flex-1">
        <h4 class="text-xl font-black text-secondary leading-tight group-hover:text-primary transition-colors mb-6">
            {{ $activity->title }}
        </h4>

        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-dashed border-gray-100 pb-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Recompensa</span>
                <span class="text-sm font-black text-primary">+{{ $activity->base_xp }} XP</span>
            </div>

            <div class="flex items-center justify-between border-b border-dashed border-gray-100 pb-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tempo Limite</span>
                <span class="text-sm font-black text-secondary">{{ $activity->time_limit_minutes ? $activity->time_limit_minutes . ' min' : 'Ilimitado' }}</span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Data Entrega</span>
                <span class="text-[11px] font-bold {{ $isExpired ? 'text-red-500' : 'text-secondary' }}">
                    {{ $deadline ? $deadline->format('d/m/Y H:i') : 'Sem prazo' }}
                </span>
            </div>
        </div>
    </div>

    <div class="px-6 py-6 bg-gray-50/50 border-t border-gray-100">
        @if($status === 'evaluated')
            <a href="#" class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-primary hover:text-primary transition-all shadow-sm">
                Ver Avaliação
            </a>
        @elseif($status === 'waiting_evaluation')
            <div class="w-full py-3 text-center text-gray-400 font-black text-[10px] uppercase tracking-widest italic">
                Aguardando Correção
            </div>
        @elseif($isExpired && $status === 'pending')
            <div class="w-full py-3 text-center text-red-400 font-black text-[10px] uppercase tracking-widest border border-red-100 rounded-2xl bg-red-50">
                Missão Expirada
            </div>
        @else
            @php
    $submission = $activity->submissions->where('student_id', auth()->id())->first();
    $status = $submission ? $submission->status : 'pending';
    // Se está em andamento, vai direto para o play. Se não, vai para o show (briefing)
    $targetRoute = ($status === 'in_progress') 
        ? route('student.activities.play', $activity) 
        : route('student.activities.show', $activity);
@endphp

<a href="{{ $targetRoute }}" 
   target="_blank" 
   class="w-full inline-flex justify-center items-center px-4 py-4 bg-primary text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-primary/30 hover:brightness-110 hover:-translate-y-1 transition-all">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
    {{ $status === 'in_progress' ? 'Continuar Atividade' : 'Iniciar Atividade' }}
</a>
        @endif
    </div>
</div>
@endif
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="bg-gray-50 inline-flex p-6 rounded-full mb-4">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-gray-500 font-bold uppercase text-sm tracking-widest">Nenhuma missão ativa por enquanto.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>