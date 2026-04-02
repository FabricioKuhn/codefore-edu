<x-app-layout>
    <x-slot name="header">  
        <x-breadcrumbs :links="[
    ['name' => 'Home', 'url' => route('dashboard')],
    ['name' => 'Minhas Turmas', 'url' => route('classrooms.index')],
    ['name' => $classroom->name, 'url' => route('classrooms.show', $classroom)],
    ['name' => 'Nova Missão', 'url' => '#']
]" />
        <h2 class="text-xl font-semibold text-[#333333] leading-tight">
            {{ __('Nova Missão') }} - {{ $classroom->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <form method="POST" action="{{ route('classrooms.activities.store', $classroom) }}">
                    @csrf

                    <!-- Título da Missão -->
                    <div class="mb-4">
                        <x-input-label for="title" value="Título da Missão" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Descrição -->
                    <div class="mb-4">
                        <x-input-label for="description" value="Descrição" />
                        <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-codeforce-green focus:ring-codeforce-green rounded-md shadow-sm" rows="4">{{ old('description') }}</textarea>
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

                    <div class="flex items-center justify-end mt-4 text-right">
                        <a href="{{ route('classrooms.show', $classroom) }}" class="text-sm text-[#333333] hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-codeforce-green mr-4 font-semibold">
                            Cancelar
                        </a>
                        <x-primary-button>
                            Salvar Missão (Rascunho)
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
