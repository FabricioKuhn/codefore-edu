<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-secondary leading-tight">
                Resultado: <span class="text-primary">{{ $activity->title }}</span>
            </h2>
            <a href="{{ route('student.classrooms.show', $activity->classroom_id) }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-primary transition">
                ← Voltar para a Turma
            </a>
        </div>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        {{-- CARD DE PONTUAÇÃO GERAL --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8 flex flex-col md:flex-row items-center gap-8">
            <div class="w-32 h-32 rounded-full border-8 border-primary/10 flex flex-col items-center justify-center bg-primary/5 shrink-0">
                <span class="text-3xl font-black text-primary">{{ $submission->earned_xp }}</span>
                <span class="text-[10px] font-black text-primary/50 uppercase">XP Ganho</span>
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <h3 class="text-2xl font-black text-secondary mb-2">Missão Cumprida!</h3>
                <p class="text-gray-500 font-medium leading-relaxed">
                    Você completou esta atividade e ela já foi revisada pelo professor. Confira abaixo o seu desempenho detalhado.
                </p>
                
                @if($submission->feedback)
                    <div class="mt-6 p-4 bg-gray-50 rounded-2xl border-l-4 border-primary italic text-gray-600 text-sm">
                        "{{ $submission->feedback }}"
                    </div>
                @endif
            </div>
        </div>

        {{-- LISTA DE QUESTÕES E GABARITO --}}
        <div class="space-y-6">
            @foreach($questions as $question)
                @php
                    $studentAnswer = $submission->answers[$question->id] ?? null;
                    // Tenta pegar o feedback individual da questão se você salvou no teacher_notes
                    $qFeedback = $submission->teacher_notes['question_feedbacks'][$question->id] ?? null;
                    $qScore = $submission->teacher_notes['scores'][$question->id] ?? null;
                @endphp

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Questão {{ $loop->iteration }}</span>
                        @if($question->type === 'multiple_choice')
                            @php
                                $correctOptionIndex = $question->options->search(fn($opt) => $opt->is_correct);
                                $isCorrect = (string)$studentAnswer === (string)$correctOptionIndex;
                            @endphp
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $isCorrect ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                {{ $isCorrect ? 'Acertou' : 'Errou' }}
                            </span>
                        @else
                           <span class="px-2 py-0.5 bg-blue-100 text-blue-600 rounded text-[9px] font-black uppercase">Avaliada ({{ $qScore }}%)</span>
                        @endif
                    </div>

                    <h4 class="text-lg font-bold text-secondary mb-6">{!! $question->statement !!}</h4>

                    {{-- Resposta do Aluno --}}
                    <div class="space-y-4">
                        @if($question->type === 'multiple_choice')
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($question->options as $key => $option)
                                    @php
                                        $isChosen = (string)$studentAnswer === (string)$key;
                                        $isRight = (bool)$option->is_correct;
                                    @endphp
                                    <div class="flex items-center gap-3 p-3 rounded-xl border {{ $isChosen ? ($isRight ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50') : ($isRight ? 'border-green-100 bg-green-50/30' : 'border-gray-50 opacity-60') }}">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black {{ $isChosen ? ($isRight ? 'bg-green-500 text-white' : 'bg-red-500 text-white') : 'bg-gray-200 text-gray-400' }}">
                                            {{ chr(97 + $loop->index) }}
                                        </div>
                                        <span class="text-sm {{ $isChosen ? 'font-bold text-secondary' : 'text-gray-500' }}">{{ $option->content }}</span>
                                        @if($isRight) <span class="ml-auto text-[9px] font-black text-green-600 uppercase">Correta</span> @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">Sua Resposta:</span>
                                <p class="text-sm text-secondary italic">"{{ $studentAnswer ?: 'Sem resposta.' }}"</p>
                            </div>
                            
                            <div class="p-4 bg-green-50/50 rounded-xl border border-green-100">
                                <span class="text-[9px] font-black text-green-600 uppercase block mb-1">Resposta Esperada:</span>
                                <p class="text-sm text-green-800 font-medium">{{ $question->expected_answer }}</p>
                            </div>
                        @endif

                        {{-- Comentário do Professor na Questão --}}
                        @if($qFeedback)
                            <div class="mt-4 p-4 bg-amber-50 rounded-xl border-l-4 border-amber-400 text-xs text-amber-900">
                                <strong>Feedback do Professor:</strong> {{ $qFeedback }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>