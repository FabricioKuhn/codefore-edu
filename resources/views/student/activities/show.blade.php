<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }} | Arena</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: {{ $tenant->primary_color ?? '#f97316' }};
            --secondary-color: {{ $tenant->secondary_color ?? '#334155' }};
            --tertiary-color: {{ $tenant->tertiary_color ?? '#ffffff' }};
        }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        
        <div class="mb-8">
            <span class="bg-primary/10 text-primary text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-full border border-primary/20">
                {{ $activity->type === 'exam' ? 'Prova' : 'Tarefa' }}
            </span>
        </div>

        <div class="text-center max-w-2xl mb-12">
            <h1 class="text-4xl md:text-5xl font-black text-secondary mb-6 tracking-tight">
                {{ $activity->title }}
            </h1>
            <p class="text-gray-500 text-lg font-medium leading-relaxed">
                {{ $activity->description ?? 'Leia as questões com atenção. Boa sorte!' }}
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <div class="bg-white px-8 py-6 rounded-3xl shadow-sm border border-gray-100 text-center min-w-[160px]">
                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1">XP</span>
                <span class="text-2xl font-black text-primary">+{{ $activity->base_xp }}</span>
            </div>
            <div class="bg-white px-8 py-6 rounded-3xl shadow-sm border border-gray-100 text-center min-w-[160px]">
                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1">Tempo</span>
                <span class="text-2xl font-black text-secondary">{{ $activity->time_limit_minutes ?? '∞' }}'</span>
            </div>
            <div class="bg-white px-8 py-6 rounded-3xl shadow-sm border border-gray-100 text-center min-w-[160px]">
                <span class="block text-[10px] font-black text-gray-400 uppercase mb-1">Questões</span>
                <span class="text-2xl font-black text-secondary">
                    {{ $activity->type === 'exam' ? array_sum($activity->exam_settings ?? []) : $activity->questions->count() }}
                </span>
            </div>
        </div>

        <form action="{{ route('student.activities.start', $activity) }}" method="POST" class="w-full max-w-xs">
            @csrf
            <button type="submit" class="w-full bg-primary hover:brightness-110 text-white font-black py-5 rounded-full shadow-2xl shadow-primary/40 transition-all uppercase tracking-widest text-lg hover:-translate-y-1 active:scale-95">
                Começar Tarefa
            </button>
        </form>

        <p class="mt-8 text-gray-400 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
            O cronômetro não pode ser pausado
        </p>
    </div>
</body>
</html>