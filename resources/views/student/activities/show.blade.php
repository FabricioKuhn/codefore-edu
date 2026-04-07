<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route('student.dashboard')],
            ['name' => $activity->classroom->name, 'url' => route('student.classrooms.show', $activity->classroom)],
            ['name' => 'Instruções', 'url' => '#']
        ]" />
        <h2 class="text-xl font-bold text-secondary leading-tight mt-2">Prepare-se para a Missão</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-100">
                <div class="bg-secondary p-10 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="bg-primary text-[10px] font-black uppercase px-3 py-1 rounded-lg tracking-widest">
                                {{ $activity->type === 'exam' ? 'Avaliação Oficial' : 'Treinamento / Tarefa' }}
                            </span>
                        </div>
                        <h3 class="text-3xl font-black">{{ $activity->title }}</h3>
                    </div>
                    <div class="absolute right-[-20px] bottom-[-20px] opacity-10">
                        <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                </div>

                <div class="p-10">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 text-center">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Recompensa</span>
                            <span class="text-2xl font-black text-primary">+{{ $activity->base_xp }} XP</span>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 text-center">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tempo Limite</span>
                            <span class="text-2xl font-black text-secondary">{{ $activity->time_limit_minutes ?? '---' }} min</span>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 text-center">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Questões</span>
                            <span class="text-2xl font-black text-secondary">
                                {{ $activity->type === 'exam' ? array_sum($activity->exam_settings ?? []) : $activity->questions->count() }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-10">
                        <h4 class="text-secondary font-black uppercase text-[10px] tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Briefing da Atividade
                        </h4>
                        <div class="text-gray-600 leading-relaxed font-medium bg-blue-50/30 p-6 rounded-2xl border border-blue-100/50">
                            {{ $activity->description ?? 'O professor não enviou instruções adicionais para esta missão. Boa sorte!' }}
                        </div>
                    </div>

                    <form action="{{ route('student.activities.start', $activity) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-primary hover:brightness-110 text-white font-black py-5 rounded-2xl shadow-lg shadow-primary/30 transition-all uppercase tracking-widest flex items-center justify-center gap-3 text-lg hover:-translate-y-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            Iniciar Missão Agora
                        </button>
                    </form>
                    
                    <div class="mt-6 flex items-center justify-center gap-2 text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-[10px] uppercase font-black tracking-widest">O cronômetro não pode ser pausado após o início.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>