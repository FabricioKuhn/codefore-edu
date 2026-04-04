<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-secondary leading-tight">
            Turma: {{ $classroom->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-secondary">{{ $classroom->name }}</h3>
                    <p class="text-gray-500 mt-2">{{ $classroom->subject }}</p>
                </div>
                <div class="mt-4 md:mt-0 text-center bg-gray-100 p-4 rounded-lg">
                    <span class="text-sm uppercase text-gray-500 font-semibold tracking-wider">Professor</span>
                    <div class="text-xl font-bold mt-1 text-primary">{{ $classroom->teacher->name }}</div>
                </div>
            </div>

            <h3 class="text-xl font-bold text-secondary mb-4 px-2">Atividades Disponíveis</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($classroom->activities as $activity)
                    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 border-t-4 border-t-codeforce-green p-6 hover:shadow-md transition">
                        <h4 class="text-lg font-bold mb-2 text-secondary">{{ $activity->title }}</h4>
                        
                        <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                            <span class="font-medium text-primary">+{{ $activity->base_xp }} XP</span>
                            <span class="px-2 py-1 bg-gray-100 rounded-md text-xs font-semibold uppercase tracking-wider">Pendente</span>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow hover:shadow-lg transition-all duration-200 hover:brightness-90">Jogar Atividade</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500 border border-gray-100">
                        Nenhuma atividade disponível nesta turma ainda.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
