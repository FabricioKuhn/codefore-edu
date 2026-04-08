<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route('student.dashboard')],
            ['name' => $classroom->name, 'url' => route('student.classrooms.show', $classroom)],
            ['name' => $lesson->title ?? 'Aula', 'url' => '#']
        ]" />
    </x-slot>

    <div class="py-1 max-w-5xl mx-auto sm:px-6 lg:px-8">
        
        {{-- CABEÇALHO DA AULA --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="px-3 py-1 bg-primary/10 text-primary rounded-lg text-[10px] font-black uppercase tracking-widest mb-4 inline-block">
                    Aula {{ $lesson->date ? $lesson->date->format('d/m') : '' }}
                </span>
                <h1 class="text-3xl font-black text-secondary leading-tight">{{ $lesson->title }}</h1>
            </div>
            
            @if($lesson->xp_reward > 0)
            <div class="bg-gray-50 px-6 py-4 rounded-2xl border border-gray-100 text-center shrink-0">
                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Recompensa</span>
                <div class="text-2xl font-black text-primary">+{{ $lesson->xp_reward }} XP</div>
            </div>
            @endif
        </div>

        {{-- CONTEÚDO DIVIDIDO EM COLUNAS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- COLUNA PRINCIPAL ESQUERDA (Resumo e Vídeo) --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- 1. RESUMO DA AULA --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-sm font-black text-secondary uppercase tracking-widest mb-4">Resumo da Aula</h3>
                    <div class="text-gray-600 font-medium leading-relaxed prose max-w-none">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>
                </div>

                {{-- 2. VÍDEO PRINCIPAL --}}
                @if($lesson->video_url)
                    @php
                        $embedUrl = $lesson->video_url;
                        if (str_contains($embedUrl, 'watch?v=')) {
                            $embedUrl = str_replace('watch?v=', 'embed/', $embedUrl);
                        } elseif (str_contains($embedUrl, 'youtu.be/')) {
                            $embedUrl = str_replace('youtu.be/', 'youtube.com/embed/', $embedUrl);
                        }
                    @endphp
                    <div class="rounded-3xl overflow-hidden shadow-lg border border-gray-100 bg-black aspect-video">
                        <iframe src="{{ $embedUrl }}" class="w-full h-full" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                    </div>
                @endif
            </div>

            {{-- COLUNA LATERAL DIREITA (Backup, Missões e Conclusão) --}}
            <div class="space-y-6">
                
                {{-- Material Backup --}}
                @if($lesson->main_material_path)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Material Backup</h3>
                    <a href="{{ asset('storage/' . $lesson->main_material_path) }}" target="_blank" download class="flex items-center justify-center gap-3 p-4 bg-gray-50 border border-gray-200 rounded-2xl hover:border-primary hover:text-primary transition group">
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="text-xs font-black text-secondary group-hover:text-primary uppercase tracking-widest">Baixar PDF</span>
                    </a>
                </div>
                @endif

                {{-- Missões da Aula (Com Status Inteligente) --}}
                @if($lesson->activities->count() > 0)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Missões da Aula</h3>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($lesson->activities->where('status', 'active') as $activity)
                            @php
                                // Busca a submissão do aluno para saber o status
                                $submission = $activity->submissions->where('student_id', auth()->id())->first();
                                $status = $submission ? $submission->status : 'pending';
                                
                                $isEvaluating = $status === 'waiting_evaluation';
                                $isEvaluated = in_array($status, ['evaluated', 'completed']);
                                $isInProgress = $status === 'in_progress';

                                // Se estiver em correção, bloqueamos o clique transformando em DIV. Senão, é um LINK.
                                $tag = $isEvaluating ? 'div' : 'a';
                                $href = $isEvaluating ? '' : 'href="'.route('student.activities.show', $activity).'"';
                            @endphp

                            <{{ $tag }} {!! $href !!} class="group block p-4 border rounded-2xl transition-all {{ $isEvaluating ? 'border-yellow-100 bg-yellow-50/30' : 'border-gray-100 bg-white hover:border-primary hover:bg-primary/5 cursor-pointer' }}">
                                
                                <div class="flex items-center gap-3 {{ !$isEvaluating ? 'mb-3' : '' }}">
                                    {{-- Ícone Dinâmico --}}
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $isEvaluated ? 'bg-green-50 text-green-500' : ($isEvaluating ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-50 text-blue-500') }}">
                                        @if($isEvaluated)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($isEvaluating)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @endif
                                    </div>
                                    
                                    {{-- Textos e Badges --}}
                                    <div class="flex-1 truncate">
                                        <h4 class="font-bold text-sm truncate {{ $isEvaluating ? 'text-yellow-800' : 'text-secondary group-hover:text-primary transition-colors' }}">{{ $activity->title }}</h4>
                                        
                                        @if($isEvaluated)
                                            <span class="text-[9px] font-black text-green-600 uppercase tracking-widest bg-green-100 px-2 py-0.5 rounded-md">Concluída</span>
                                        @elseif($isEvaluating)
                                            <span class="text-[9px] font-black text-yellow-600 uppercase tracking-widest">Em Correção</span>
                                        @elseif($isInProgress)
                                            <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest animate-pulse">Em Andamento</span>
                                        @else
                                            <span class="text-[9px] font-black text-primary uppercase">{{ $activity->base_xp }} XP</span>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Botões Inferiores (Somem se estiver em correção) --}}
                                @if($isEvaluated)
                                    <div class="w-full text-center py-2 mt-3 bg-green-50 text-green-600 group-hover:bg-green-500 group-hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors">
                                        Ver Resultado
                                    </div>
                                @elseif(!$isEvaluating)
                                    <div class="w-full text-center py-2 mt-3 bg-gray-50 group-hover:bg-primary group-hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors">
                                        {{ $isInProgress ? 'Continuar Missão' : 'Acessar Missão' }}
                                    </div>
                                @endif
                                
                            </{{ $tag }}>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Botão de Conclusão --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    @if($isCompleted)
                        <div class="flex flex-col items-center justify-center text-center py-4">
                            <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <h4 class="font-black text-secondary">Aula Concluída!</h4>
                            <p class="text-xs font-bold text-gray-400 uppercase mt-1">XP já creditado</p>
                        </div>
                    @else
                        <form action="{{ route('student.lessons.complete', [$classroom, $lesson]) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-primary hover:brightness-110 text-white font-black py-4 rounded-2xl shadow-lg shadow-primary/30 transition-all uppercase tracking-widest text-xs hover:-translate-y-1 active:scale-95">
                                Marcar como Concluída
                            </button>
                            <p class="text-[10px] text-gray-400 font-bold text-center mt-3 uppercase">Conclua para ganhar XP</p>
                        </form>
                    @endif
                </div>

            </div>
        </div>

        {{-- VISUALIZADOR DE PDF NA TELA (Fora do Grid, ocupando largura total) --}}
        @if($lesson->main_material_path)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 flex flex-col h-[600px] md:h-[900px] mt-8">
                <h3 class="text-sm font-black text-secondary uppercase tracking-widest mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Material de Estudo (PDF)
                </h3>
                <iframe src="{{ asset('storage/' . $lesson->main_material_path) }}" class="w-full flex-1 rounded-2xl border border-gray-200 bg-gray-50 shadow-inner" title="Visualizador de PDF"></iframe>
            </div>
        @endif

    </div>
</x-app-layout>
