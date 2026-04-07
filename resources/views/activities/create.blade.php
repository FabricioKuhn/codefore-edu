<x-app-layout>
    <x-slot name="header">  
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')],
            ['name' => 'Minhas Turmas', 'url' => route(auth()->user()->role . '.classrooms.index')],
            ['name' => $classroom->name, 'url' => route(auth()->user()->role . '.classrooms.show', $classroom)],
            ['name' => 'Nova Avaliação', 'url' => '#']
        ]" />
        <h2 class="text-xl font-semibold text-secondary leading-tight mt-2">
            {{ __('Nova Avaliação') }} - {{ $classroom->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 p-8" x-data="{ tipo: '{{ old('type', 'task') }}' }">
                <form method="POST" action="{{ route(auth()->user()->role . '.activities.store') }}">
                    @csrf
                    <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">

                    <div class="mb-8 p-6 bg-blue-50/50 border border-blue-100 rounded-xl">
                        <x-input-label for="type" value="Formato da Avaliação" class="text-blue-800 font-black uppercase tracking-widest text-xs mb-2" />
                        <select id="type" name="type" x-model="tipo" class="block w-full md:w-1/2 border-blue-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm font-bold text-secondary cursor-pointer">
                            <option value="task">Tarefa (Alunos respondem exatamente as questões selecionadas)</option>
                            <option value="exam">Prova Dinâmica (Sistema sorteia as questões por aluno)</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div x-show="tipo === 'exam'" x-cloak class="mb-8 p-6 bg-purple-50 border border-purple-100 rounded-xl transition-all">
                        <h3 class="text-purple-800 font-black uppercase tracking-widest text-xs mb-4">Configuração do Sorteio da Prova</h3>
                        <p class="text-xs text-purple-600 mb-4">Defina quantas questões de cada tipo o sistema deve sortear do seu Banco para cada aluno.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="exam_mc" value="Qtd. Múltipla Escolha" />
                                <x-text-input id="exam_mc" class="block mt-1 w-full rounded-xl" type="number" name="exam_settings[multiple_choice]" :value="old('exam_settings.multiple_choice', 0)" min="0" />
                            </div>
                            <div>
                                <x-input-label for="exam_desc" value="Qtd. Descritivas" />
                                <x-text-input id="exam_desc" class="block mt-1 w-full rounded-xl" type="number" name="exam_settings[descriptive]" :value="old('exam_settings.descriptive', 0)" min="0" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <x-input-label for="title" value="Título da Avaliação" />
                            <x-text-input id="title" class="block mt-1 w-full rounded-xl" type="text" name="title" :value="old('title')" required autofocus placeholder="Ex: Prova Bimestral de História" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description" value="Instruções / Descrição" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm" rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 border-t border-gray-100 pt-8">
                        <div>
                            <x-input-label for="start_date" value="Data e Hora de Início" />
                            <x-text-input id="start_date" class="block mt-1 w-full rounded-xl text-sm" type="datetime-local" name="start_date" :value="old('start_date')" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="end_date" value="Prazo Final de Entrega" />
                            <x-text-input id="end_date" class="block mt-1 w-full rounded-xl text-sm" type="datetime-local" name="end_date" :value="old('end_date')" />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="time_limit_minutes" value="Cronômetro (Minutos)" />
                            <x-text-input id="time_limit_minutes" class="block mt-1 w-full rounded-xl text-sm" type="number" name="time_limit_minutes" :value="old('time_limit_minutes')" placeholder="Deixe em branco p/ ilimitado" />
                            <x-input-error :messages="$errors->get('time_limit_minutes')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="base_xp" value="XP Base (Recompensa)" />
                            <x-text-input id="base_xp" class="block mt-1 w-full rounded-xl text-sm border-codeforce-green focus:ring-codeforce-green" type="number" name="base_xp" :value="old('base_xp', 100)" required min="1" />
                            <x-input-error :messages="$errors->get('base_xp')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100 bg-gray-50 -mx-8 -mb-8 px-8 pb-8 rounded-b-xl">
                        <a href="{{ route(auth()->user()->role . '.classrooms.show', $classroom) }}" class="text-sm text-gray-500 hover:text-gray-800 font-bold tracking-widest uppercase mr-6 transition">
                            Cancelar
                        </a>
                        <x-primary-button class="px-8 py-3">
                            Salvar e Configurar Questões
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>