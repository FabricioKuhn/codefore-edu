<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> [x-cloak] { display: none !important; } 
        :root {
        /* Se houver tenant, usa a cor dele. Se não, usa o verde CodeForce */
        --primary-color: {{ $tenant->primary_color ?? '#00ad9a' }};
        --secondary-color: {{ $tenant->secondary_color ?? '#333333' }};
        --tertiary-color: {{ $tenant->tertiary_color ?? '#ffffff' }};
    }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased select-none">

    <div x-data="quizApp()" x-init="initTimer()" class="min-h-screen flex flex-col" x-cloak>
        
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-5xl mx-auto px-6 h-20 flex items-center justify-between">
                
                <div class="flex items-center gap-4">
                    <div class="h-10">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </div>
                    
                    <div class="h-8 w-[1px] bg-gray-200 mx-2"></div>

                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Progresso</span>
                        <div class="flex items-center gap-3">
                            <div class="h-1.5 w-32 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary transition-all duration-500" :style="'width: ' + progress + '%'"></div>
                            </div>
                            <span class="text-[10px] font-bold text-secondary" x-text="(step + 1) + '/' + totalSteps"></span>
                        </div>
                    </div>
                </div>

                @if($activity->time_limit_minutes)
                <div class="flex items-center gap-3 bg-primary px-5 py-2.5 rounded-2xl shadow-xl">
                    <svg :class="isUrgent ? 'text-red-400 animate-pulse' : 'text-tertiary'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-mono font-black text-xl text-white" x-text="timeLeftFormatted"></span>
                </div>
                @endif
            </div>
        </nav>

        <main class="flex-1 flex flex-col items-center justify-center p-6 mb-24">
            <form id="arenaForm" action="{{ route('student.activities.submit', $activity) }}" method="POST" class="w-full max-w-3xl">
                @csrf
                
                @foreach($questions as $index => $question)
<div x-show="step === {{ $index }}" x-transition.opacity.duration.300ms class="space-y-8">
    
    <div class="text-center space-y-6">
        <h2 class="text-2xl md:text-3xl font-bold text-[#1e293b] leading-snug">
            {!! $question->statement !!}
        </h2>

        @if($question->external_link)
            <div class="flex justify-center">
                <a href="{{ $question->external_link }}" target="_blank" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-50 text-blue-600 border border-blue-200 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    {{ $question->external_link_label ?? 'Ver Recurso Externo' }}
                </a>
            </div>
        @endif
    </div>

    @if(!empty($question->attachments))
        <div class="flex flex-wrap justify-center gap-4 mt-4">
            {{-- Proteção adicionada aqui com ?? [] --}}
            @foreach($question->attachments ?? [] as $img)
                <div class="relative group cursor-zoom-in">
                    <img src="{{ asset('storage/'.$img) }}" 
                         @click="openLightbox('{{ asset('storage/'.$img) }}')"
                         class="rounded-xl shadow-sm max-w-full max-h-40 md:max-h-48 w-auto h-auto border-2 border-white transition-all hover:scale-105">
                    
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/5 rounded-xl pointer-events-none">
                        <svg class="w-6 h-6 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($question->guidelines)
        <div x-data="{ showHint: false }" class="max-w-2xl mx-auto w-full">
            <button type="button" @click="showHint = !showHint" 
                    class="flex items-center gap-2 text-primary font-black uppercase text-[10px] tracking-[0.2em] hover:opacity-80 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                <span x-text="showHint ? 'Ocultar Dica' : 'Exibir Dica do Professor'"></span>
            </button>
            
            <div x-show="showHint" x-transition 
                 class="mt-4 p-6 bg-amber-50 border border-amber-100 rounded-[1.5rem] text-amber-900 text-sm font-medium leading-relaxed shadow-sm">
                {!! $question->guidelines !!}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-3 max-w-2xl mx-auto w-full pt-4">
        @if($question->type === 'multiple_choice')
            @php $letters = ['a', 'b', 'c', 'd', 'e']; @endphp
            {{-- Proteção adicionada aqui com ?? [] --}}
            @foreach($question->options ?? [] as $key => $option)
                <label class="relative flex items-center p-4 bg-white rounded-2xl border-2 border-gray-200 shadow-sm cursor-pointer transition-all hover:border-primary/40 group active:scale-[0.99] has-[:checked]:border-primary has-[:checked]:bg-primary/[0.03]">
                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" class="hidden" @change="autoNext ? setTimeout(() => next(), 400) : null">
                    
                    <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 font-bold flex items-center justify-center text-sm border-2 border-gray-100 group-has-[:checked]:bg-primary group-has-[:checked]:text-white group-has-[:checked]:border-primary transition-all shrink-0">
                        {{ $letters[$loop->index] ?? $key }}
                    </div>
                    
                    <span class="ml-4 text-[15px] font-medium text-gray-700 leading-snug">
                        {{ is_object($option) ? $option->content : $option['content'] }}
                    </span>
                </label>
            @endforeach
        @else
            <div class="bg-white p-2 rounded-3xl border-2 border-gray-200 shadow-inner">
                <textarea name="answers[{{ $question->id }}]" rows="5" class="w-full border-0 focus:ring-0 rounded-2xl text-base font-medium p-6 text-secondary placeholder-gray-300" placeholder="Escreva sua resposta detalhada aqui..."></textarea>
            </div>
        @endif
    </div>
</div>
@endforeach

                <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 p-6 shadow-[0_-10px_40px_rgba(0,0,0,0.03)]">
                    <div class="max-w-3xl mx-auto flex justify-between items-center px-4">
                        
                        <button type="button" @click="prev()" x-show="step > 0" 
                                class="flex items-center gap-2 px-6 py-3 rounded-xl text-gray-500 font-bold uppercase text-[10px] tracking-widest hover:bg-gray-50 transition-all border border-gray-200">
                            Anterior
                        </button>
                        
                        <div x-show="step === 0" class="w-1"></div>

                        <template x-if="step < totalSteps - 1">
                            <button type="button" @click="next()" 
                                    class="bg-primary text-tertiary px-8 py-4 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-lg hover:bg-primary transition-all">
                                Próxima Questão
                            </button>
                        </template>

                        <template x-if="step === totalSteps - 1">
                            <button type="submit" 
                                    class="bg-primary color-primary text-tertiary px-10 py-4 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-primary hover:bg-primary transition-all">
                                Enviar respostas
                            </button>
                        </template>
                    </div>
                </div>
            </form>
            <div x-show="lightboxOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.away="lightboxOpen = false"
     @keydown.escape.window="lightboxOpen = false"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm" 
     style="display: none;">
    
    <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>

    <img :src="lightboxImg" 
         class="max-w-full max-h-[90vh] rounded-lg shadow-2xl border-4 border-white/10 shadow-black"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="scale-90"
         x-transition:enter-end="scale-100">
</div>
        </main>
        
    </div>

    <script>
        function quizApp() {
            return {
                step: 0,
                totalSteps: {{ count($questions) }},
                autoNext: true,
                limitMinutes: {{ $activity->time_limit_minutes ?? 0 }},
                startedAt: '{{ $submission->started_at->toIso8601String() }}',
                timeLeft: 0,
                isUrgent: false,
                lightboxOpen: false, // Variável de controle
                lightboxImg: '',     // Fonte da imagem

                openLightbox(src) {
                    this.lightboxImg = src;
                    this.lightboxOpen = true;
                },

                get progress() { return ((this.step + 1) / this.totalSteps) * 100; },
                next() { if (this.step < this.totalSteps - 1) this.step++; },
                prev() { if (this.step > 0) this.step--; },

                initTimer() {
                    if (this.limitMinutes === 0) return;
                    const update = () => {
                        const elapsed = Math.floor((new Date().getTime() - new Date(this.startedAt).getTime()) / 1000);
                        this.timeLeft = Math.max(0, (this.limitMinutes * 60) - elapsed);
                        this.isUrgent = this.timeLeft < 300;
                        if (this.timeLeft <= 0) document.getElementById('arenaForm').submit();
                    };
                    update(); setInterval(update, 1000);
                },

                get timeLeftFormatted() {
                    const m = Math.floor(this.timeLeft / 60);
                    const s = this.timeLeft % 60;
                    return `${m}:${s.toString().padStart(2, '0')}`;
                }
            }
        }
    </script>
</body>
</html>