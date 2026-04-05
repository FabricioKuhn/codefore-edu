<x-app-layout>
    <x-slot name="header">  
        <x-breadcrumbs :links="[
    ['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')],
    ['name' => 'Minhas Turmas', 'url' => route(auth()->user()->role . '.classrooms.index')],
    ['name' => $classroom->name, 'url' => route(auth()->user()->role . '.classrooms.show', $classroom)],
    ['name' => 'Nova Atividade', 'url' => '#']
]" />
        <h2 class="text-xl font-semibold text-secondary leading-tight">
            {{ __('Nova Atividade') }} - {{ $classroom->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <form method="POST" action="{{ route(auth()->user()->role . '.activities.store') }}">
                    @csrf
                    <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">

                    <!-- Título da Atividade -->
                    <div class="mb-4">
                        <x-input-label for="title" value="Título da Atividade" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Descrição -->
                    <div class="mb-4">
                        <x-input-label for="description" value="Descrição" />
                        <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm" rows="4">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- XP Base -->
                        <div>
                            <x-input-label for="base_xp" value="XP Base" />
                            <x-text-input id="base_xp" class="block mt-1 w-full" type="number" name="base_xp" :value="old('base_xp', 100)" required min="1" />
                            <x-input-error :messages="$errors->get('base_xp')" class="mt-2" />
                        </div>

                        <!-- Taxa de Conversão -->
                        <div>
                            <x-input-label for="coin_conversion_rate" value="Taxa de Conversão de Coins" />
                            <x-text-input id="coin_conversion_rate" class="block mt-1 w-full" type="number" step="0.01" name="coin_conversion_rate" :value="old('coin_conversion_rate', 0.10)" required min="0" max="1" />
                            <x-input-error :messages="$errors->get('coin_conversion_rate')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <div>
                            <x-input-label for="start_date" value="Data de Início" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="datetime-local" name="start_date" :value="old('start_date')" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="end_date" value="Data de Término" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="datetime-local" name="end_date" :value="old('end_date')" />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="duration_minutes" value="Tempo (Minutos)" />
                            <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" :value="old('duration_minutes')" placeholder="Opcional" />
                            <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" value="Status Inicial" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Ativa</option>
                                <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Encerrada</option>
                                <option value="canceled" {{ old('status') == 'canceled' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="shuffle_options" value="1" class="sr-only peer" {{ old('shuffle_options') ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-3 text-sm font-bold text-gray-700 uppercase tracking-widest">Embaralhar Respostas</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4 text-right">
                        <a href="{{ route(auth()->user()->role . '.classrooms.show', $classroom) }}" class="text-sm text-secondary hover:text-primary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-4 font-semibold">
                            Cancelar
                        </a>
                        <x-primary-button>
                            Salvar Atividade (Rascunho)
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
