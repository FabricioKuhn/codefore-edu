<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
    ['name' => 'Home', 'url' => route('dashboard')],
    ['name' => 'Minhas Turmas', 'url' => route('classrooms.index')],
    ['name' => 'Nova Turma', 'url' => '#']
]" />
        <h2 class="text-xl font-semibold text-[#333333] leading-tight">
            {{ __('Nova Turma') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <form method="POST" action="{{ route('classrooms.store') }}">
                    @csrf

                    <!-- Nome da Turma -->
                    <div class="mb-4">
                        <x-input-label for="name" value="Nome da Turma" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Disciplina -->
                    <div class="mb-4">
                        <x-input-label for="subject" value="Disciplina" />
                        <x-text-input id="subject" class="block mt-1 w-full" type="text" name="subject" :value="old('subject')" required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- XP Base Padrão -->
                        <div>
                            <x-input-label for="base_xp_level" value="XP Base Padrão" />
                            <x-text-input id="base_xp_level" class="block mt-1 w-full" type="number" name="base_xp_level" :value="old('base_xp_level', 100)" />
                            <x-input-error :messages="$errors->get('base_xp_level')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">XP necessário para alcançar o nível 2.</p>
                        </div>

                        <!-- Fator de Crescimento Padrão -->
                        <div>
                            <x-input-label for="level_growth_factor" value="Fator de Crescimento Padrão" />
                            <x-text-input id="level_growth_factor" class="block mt-1 w-full" type="number" step="0.01" name="level_growth_factor" :value="old('level_growth_factor', 1.20)" />
                            <x-input-error :messages="$errors->get('level_growth_factor')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Multiplicador de XP a cada nível.</p>
                        </div>
                    </div>

                    <section class="mt-8">
    <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-[#333333]">Calendário e Frequência</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div>
            <x-input-label for="total_lessons" value="Total de Aulas do Curso *" />
            <x-text-input id="total_lessons" name="total_lessons" type="number" class="mt-1 block w-full" :value="old('total_lessons', $classroom->total_lessons ?? 24)" required />
        </div>

        <div>
            <x-input-label for="start_date" value="Data de Início *" />
            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', isset($classroom) ? $classroom->start_date->format('Y-m-d') : '')" required />
        </div>

        <div>
            <x-input-label for="min_attendance_percent" value="% Mínima para Aprovação" />
            <x-text-input id="min_attendance_percent" name="min_attendance_percent" type="number" step="0.1" class="mt-1 block w-full" :value="old('min_attendance_percent', $classroom->min_attendance_percent ?? 70)" />
        </div>

        <div>
            <x-input-label for="frequency" value="Frequência das Aulas" />
            <select name="frequency" id="frequency" class="mt-1 block w-full border-gray-300 focus:border-[#00ad9a] focus:ring-[#00ad9a] rounded-md shadow-sm">
                <option value="weekly">Semanal</option>
                <option value="biweekly">Quinzenal</option>
                <option value="daily">Diário (Seg a Sex)</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <x-input-label value="Dias da Semana" />
            <div class="flex flex-wrap gap-4 mt-3">
                @foreach(['1'=>'Seg', '2'=>'Ter', '3'=>'Qua', '4'=>'Qui', '5'=>'Sex', '6'=>'Sáb', '0'=>'Dom'] as $val => $label)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="days_of_week[]" value="{{ $val }}" class="rounded border-gray-300 text-[#00ad9a] shadow-sm focus:ring-[#00ad9a]">
                        <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <x-input-label for="start_time" value="Horário de Início" />
            <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" :value="old('start_time', $classroom->start_time ?? '18:30')" />
        </div>

        <div>
            <x-input-label for="end_time" value="Horário de Término" />
            <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" :value="old('end_time', $classroom->end_time ?? '20:30')" />
        </div>

        <div class="flex items-center mt-6">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" name="skip_holidays" value="1" checked class="rounded border-gray-300 text-[#00ad9a] shadow-sm focus:ring-[#00ad9a]">
                <span class="ml-2 text-sm text-gray-600">Pular Feriados</span>
            </label>
        </div>
    </div>
</section>

                    <div class="flex items-center justify-end mt-4 text-right">
                        <a href="{{ route('classrooms.index') }}" class="text-sm text-[#333333] hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-codeforce-green mr-4 font-semibold">
                            Cancelar
                        </a>
                        <x-primary-button>
                            Salvar Turma
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
