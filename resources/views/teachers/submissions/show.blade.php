<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-secondary leading-tight">
                {{-- 1. Nome da tarefa incluído no Header --}}
                <span class="text-primary">{{ $activity->title }}</span> 
                <span class="text-gray-300 mx-2">|</span> 
                <span class="text-gray-400 font-medium">Corrigindo: {{ $submission->student->name }}</span>
            </h2>
            <a href="{{ route('teacher.submissions.index', $activity) }}" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-primary transition">
                ← Voltar para a lista
            </a>
        </div>
    </x-slot>

    <div class="py-12 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <form id="evaluationForm" action="{{ route('teacher.submissions.evaluate', [$activity, $submission]) }}" method="POST">
            @csrf
            
            {{-- Adicionado items-start para permitir o sticky na coluna lateral --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                {{-- COLUNA DAS RESPOSTAS --}}
                <div class="lg:col-span-2 space-y-6">
                    @foreach($questions as $question)
                        @php
                            $studentAnswer = $submission->answers[$question->id] ?? null;
                            $weight = $question->pivot->weight_override ?? $question->weight ?? 1;
                        @endphp
                        
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-black text-gray-400">
                                    {{ $loop->iteration }}
                                </span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-primary">
                                    {{ $question->type === 'multiple_choice' ? 'Múltipla Escolha' : 'Descritiva' }}
                                </span>
                                <span class="text-[10px] font-black text-gray-300 uppercase ml-auto">Peso {{ $weight }}</span>
                            </div>

                            <h3 class="text-lg font-bold text-secondary mb-6">{!! $question->statement !!}</h3>

                            <div class="mb-8">
                                @if($question->type === 'multiple_choice')
                                    <div class="space-y-2">
                                        @php
                                            $correctOptionIndex = $question->options->search(fn($opt) => $opt->is_correct);
                                            $isCorrect = (string)$studentAnswer === (string)$correctOptionIndex;
                                        @endphp
                                        @foreach($question->options as $key => $option)
                                            @php
                                                $isChosen = (string)$studentAnswer === (string)$key;
                                                $isRightOption = (bool)$option->is_correct;
                                            @endphp
                                            <div class="flex items-center gap-3 p-3 rounded-xl border {{ $isChosen ? ($isRightOption ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') : ($isRightOption ? 'border-green-200 bg-green-50/30' : 'border-gray-100 opacity-60') }}">
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black {{ $isChosen ? ($isRightOption ? 'bg-green-500 text-white' : 'bg-red-500 text-white') : 'bg-gray-100 text-gray-400' }}">
                                                    {{ chr(97 + $loop->index) }}
                                                </div>
                                                <span class="text-sm {{ $isChosen ? 'font-bold text-secondary' : 'text-gray-500' }}">{{ $option->content }}</span>
                                                @if($isRightOption) <span class="ml-auto text-[9px] font-black text-green-600 uppercase">Gabarito</span> @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" class="question-score" data-weight="{{ $weight }}" value="{{ $isCorrect ? 100 : 0 }}">
                                @else
                                    <div class="bg-blue-50/50 border border-blue-100 p-6 rounded-2xl mb-4">
                                        <span class="text-[10px] font-black uppercase text-blue-400 block mb-2 tracking-widest">Resposta do Aluno:</span>
                                        <p class="text-secondary font-medium leading-relaxed italic">
                                            {{ $studentAnswer ?: 'O aluno não respondeu esta questão.' }}
                                        </p>
                                    </div>

                                    <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl">
                                        <span class="text-[10px] font-black text-gray-400 uppercase block mb-1">Gabarito Esperado (Professor):</span>
                                        {{-- 2. Corrigido para expected_answer --}}
                                        <p class="text-sm text-gray-600">{{ $question->expected_answer ?? 'Nenhum gabarito cadastrado.' }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-100">
                                @if($question->type === 'multiple_choice')
                                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl">
                                        <span class="text-xs font-black text-gray-400 uppercase">Avaliação Automática</span>
                                        <span class="text-lg font-black {{ $isCorrect ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $isCorrect ? number_format($weight, 1) : '0.0' }} / {{ number_format($weight, 1) }} pts
                                        </span>
                                    </div>
                                @else
                                    <div class="flex gap-4 items-end">
                                        <div class="flex-1">
                                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">Feedback da Questão</label>
                                            <input type="text" 
       name="question_feedbacks[{{ $question->id }}]" 
       value="{{ $submission->teacher_notes['question_feedbacks'][$question->id] ?? '' }}" 
       class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm" 
       placeholder="Opcional...">
                                        </div>
                                        <div class="w-32">
                                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">% de Acerto</label>
                                            <div class="relative">
                                                {{-- Procure o input de scores e deixe assim --}}
<input type="number" 
       name="scores[{{ $question->id }}]" 
       class="question-score w-full bg-white border-primary rounded-xl font-black text-primary text-xl"
       data-weight="{{ $weight }}" 
       min="0" max="100" 
       value="{{ $submission->teacher_notes['scores'][$question->id] ?? 0 }}" 
       oninput="calculateTotal()">
                                                <span class="absolute right-3 top-3 text-primary font-bold">%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- COLUNA DA AVALIAÇÃO FIXA --}}
                <div class="lg:col-span-1 lg:sticky lg:top-8">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                        <h4 class="text-sm font-black text-secondary uppercase tracking-widest mb-6">Painel de Notas</h4>
                        
                        <div class="mb-6 p-6 bg-primary/5 rounded-2xl border border-primary/10 text-center">
                            <label class="text-[10px] font-black text-primary uppercase tracking-widest block mb-1">XP Final Calculado</label>
                            <div class="flex items-center justify-center gap-1">
                                <span id="display-final-xp" class="text-4xl font-black text-primary">0</span>
                                <span class="text-sm font-bold text-primary/60 mt-2">XP</span>
                            </div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase mt-2 block">Máximo: {{ $activity->base_xp }} XP</span>
                            
                            {{-- Input escondido que será enviado --}}
                            <input type="hidden" name="earned_xp" id="input-final-xp" value="0">
                        </div>

                        <div class="mb-8">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Feedback Geral</label>
                            <textarea name="feedback" 
          rows="4" 
          class="w-full bg-gray-50 border-gray-100 rounded-2xl text-sm font-medium focus:ring-primary focus:border-primary" 
          placeholder="Comentário final sobre o desempenho...">{{ $submission->feedback }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-primary hover:brightness-110 text-white font-black py-4 rounded-2xl shadow-lg shadow-primary/30 transition-all uppercase tracking-widest text-xs">
                            Confirmar Nota
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

<script>
    function calculateTotal() {
        let totalWeight = 0;
        let earnedPoints = 0;
        const baseXP = parseInt("{{ $activity->base_xp }}") || 0;

        const inputs = document.querySelectorAll('.question-score');

        inputs.forEach(input => {
            const weight = parseFloat(input.getAttribute('data-weight')) || 0;
            const percentage = parseFloat(input.value) || 0;
            
            totalWeight += weight;
            earnedPoints += weight * (percentage / 100);
        });

        const finalPercentage = totalWeight > 0 ? (earnedPoints / totalWeight) : 0;
        const finalXP = Math.round(baseXP * finalPercentage);

        const display = document.getElementById('display-final-xp');
        const hiddenInput = document.getElementById('input-final-xp');
        
        if (display) display.innerText = finalXP;
        if (hiddenInput) hiddenInput.value = finalXP;
    }

    // Inicializa no carregamento e garante escuta
    document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
</x-app-layout>