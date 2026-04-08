<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route('student.dashboard')],
            ['name' => 'Minhas Turmas', 'url' => route('student.dashboard')],
            ['name' => $classroom->name, 'url' => '#']
        ]" />
    </x-slot>

    <div class="py-0">
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
                        {{-- CAIXA DE PROGRESSO INTELIGENTE --}}
                        <div class="px-6 py-3 rounded-2xl border text-center transition-all duration-500 {{ $progress >= 100 ? 'bg-gradient-to-br from-amber-100 to-amber-50 border-amber-300 shadow-[0_0_20px_rgba(251,191,36,0.3)]' : 'bg-gray-50 border-gray-100' }}">
                            
                            @if($progress >= 100)
                                <span class="flex items-center justify-center gap-1 text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    Platinado!
                                </span>
                                <div class="text-2xl font-black text-amber-600 drop-shadow-sm">{{ $progress }}%</div>
                            @else
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Seu Progresso</span>
                                <div class="text-2xl font-black text-secondary">{{ $progress }}%</div>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>

            {{-- TRILHA DE AULAS (CARROSSEL INTELIGENTE COM SETAS) --}}
            <div class="mb-12">
                <h3 class="text-lg font-black text-secondary mb-6 px-2 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-6 bg-primary rounded-full"></span>
                    Trilha de Aulas
                </h3>

                @php
                    $allLessons = $classroom->lessons->sortBy('date');
                    $nextLesson = $allLessons->where('date', '>=', now()->startOfDay())->where('status', '!=', 'canceled')->first() 
                                  ?? $allLessons->last();
                @endphp

                {{-- Wrapper relativo para as setas e o carrossel --}}
                <div class="relative group" x-data="{
                        scrollToNext() {
                            let nextCard = document.getElementById('lesson-card-{{ $nextLesson->id ?? 0 }}');
                            if(nextCard) {
                                setTimeout(() => {
                                    // Calcula a posição do card em relação ao container para rolar perfeitamente
                                    this.$refs.carousel.scrollTo({
                                        left: nextCard.offsetLeft - this.$refs.carousel.offsetLeft - 20,
                                        behavior: 'smooth'
                                    });
                                }, 300);
                            }
                        }
                    }" 
                    x-init="scrollToNext()">
                    
                    {{-- Seta Esquerda (Aparece no hover no Desktop) --}}
                    <button @click="$refs.carousel.scrollBy({ left: -300, behavior: 'smooth' })" 
                            class="absolute left-0 top-1/2 -translate-y-1/2 -ml-5 z-10 bg-white shadow-lg border border-gray-100 text-gray-400 hover:text-primary hover:scale-110 w-12 h-12 rounded-full hidden md:flex items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    {{-- Container do Carrossel --}}
                    <div x-ref="carousel" class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-8 pt-4 px-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                        
                        @forelse($allLessons as $lesson)
                            @php
                                $isCompleted = $lesson->studentsCompleted()->where('users.id', auth()->id())->exists();
                                $isCanceled = $lesson->status === 'canceled';
                                $isNext = isset($nextLesson) && $nextLesson->id === $lesson->id;
                                
                                // Regra: Só é acessível se o professor preencheu o conteúdo (configurou) e não está cancelada
                                $isConfigured = !empty($lesson->content);
                                $isAccessible = $isConfigured && !$isCanceled;
                                
                                // Tag dinâmica (Link se for acessível, Div se não for)
                                $tag = $isAccessible ? 'a' : 'div';
                                $href = $isAccessible ? 'href="'.route('student.lessons.show', [$classroom, $lesson]).'"' : '';
                            @endphp
                            
                            @if($isCanceled)
                                {{-- CARD CANCELADO --}}
                                <div id="lesson-card-{{ $lesson->id }}" class="snap-start shrink-0 w-[280px] bg-gray-50 p-6 rounded-3xl border border-gray-200 opacity-70 relative">
                                    <div class="absolute top-4 right-4">
                                        <span class="px-2 py-1 bg-red-100 text-red-600 rounded-md text-[9px] font-black uppercase tracking-widest">Cancelada</span>
                                    </div>
                                    <div class="flex flex-col h-full opacity-60 grayscale">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 line-through">{{ $lesson->date ? $lesson->date->format('d/m/Y') : 'S/ Data' }}</span>
                                        <h4 class="font-bold text-gray-500 text-base mb-2 line-clamp-2 line-through">{{ $lesson->title }}</h4>
                                        <div class="mt-auto pt-4 border-t border-gray-200">
                                            <span class="text-[10px] font-bold text-gray-400 line-clamp-2" title="{{ $lesson->justification }}">{{ $lesson->justification ?? 'Motivo não informado' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- CARD NORMAL OU BLOQUEADO --}}
                                <{{ $tag }} {!! $href !!} 
                                   id="lesson-card-{{ $lesson->id }}" 
                                   class="snap-start shrink-0 w-[280px] p-6 rounded-3xl border shadow-sm transition-all duration-300 relative flex flex-col {{ $isCompleted ? 'border-green-200 bg-green-50/30' : 'bg-white border-gray-100' }} {{ $isAccessible ? 'hover:border-primary hover:-translate-y-1 group cursor-pointer' : 'opacity-80' }}">
                                    
                                    {{-- Badge de Próxima Aula --}}
                                    @if($isNext)
                                        <div class="absolute -top-3 left-6 px-3 py-1 bg-primary text-white rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm z-10">
                                            Sua Próxima Aula
                                        </div>
                                    @endif

                                    <div class="flex justify-between items-start mb-4 {{ $isNext ? 'mt-2' : '' }}">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $lesson->date ? $lesson->date->format('d/m/Y') : 'S/ Data' }}</span>
                                        
                                        @if($isCompleted)
                                            <div class="bg-green-100 p-1 rounded-full">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        @elseif(!$isAccessible)
                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        @else
                                            <span class="w-2.5 h-2.5 rounded-full {{ $isNext ? 'bg-primary animate-pulse' : 'bg-gray-200' }}"></span>
                                        @endif
                                    </div>
                                    
                                    <h4 class="font-bold text-secondary text-base mb-4 line-clamp-2 flex-1 {{ $isAccessible ? 'group-hover:text-primary transition-colors' : 'text-gray-400' }}">{{ $lesson->title ?? 'Aula Agendada' }}</h4>
                                    
                                    <div class="flex items-center justify-between mt-auto pt-4 border-t {{ $isCompleted ? 'border-green-100' : 'border-gray-50' }}">
                                        <span class="text-[11px] font-black {{ $isCompleted ? 'text-green-600' : ($isAccessible ? 'text-primary' : 'text-gray-400') }} uppercase">{{ $lesson->xp_reward }} XP</span>
                                        
                                        @if($lesson->activities->count() > 0)
                                            <span class="text-[9px] px-2 py-1 bg-gray-100 text-gray-500 rounded-lg font-bold {{ $isAccessible ? 'group-hover:bg-primary/10 group-hover:text-primary transition-colors' : '' }}">{{ $lesson->activities->count() }} Tarefa(s)</span>
                                        @elseif(!$isAccessible)
                                            <span class="text-[9px] font-bold text-gray-400 uppercase">Em Breve</span>
                                        @endif
                                    </div>
                                </{{ $tag }}>
                            @endif
                        @empty
                            <div class="w-full py-8 text-center bg-white rounded-3xl border border-gray-100 shadow-sm">
                                <p class="text-gray-400 font-bold uppercase text-xs tracking-widest">Nenhuma aula na trilha ainda.</p>
                            </div>
                        @endforelse
                        
                        <div class="shrink-0 w-8"></div>
                    </div>

                    {{-- Seta Direita (Aparece no hover no Desktop) --}}
                    <button @click="$refs.carousel.scrollBy({ left: 300, behavior: 'smooth' })" 
                            class="absolute right-0 top-1/2 -translate-y-1/2 -mr-5 z-10 bg-white shadow-lg border border-gray-100 text-gray-400 hover:text-primary hover:scale-110 w-12 h-12 rounded-full hidden md:flex items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>

                </div>
            </div>

            <h3 class="text-lg font-black text-secondary mb-6 px-2 uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-6 bg-primary rounded-full"></span>
                Atividades Disponíveis
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
                Tarefa Expirada
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
                        <p class="text-gray-500 font-bold uppercase text-sm tracking-widest">Nenhuma Tarefa ativa por enquanto.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>