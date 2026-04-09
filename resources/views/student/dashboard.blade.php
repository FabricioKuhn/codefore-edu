<x-app-layout>
    <style>
        :root {
            --primary-color: {{ $tenant->primary_color ?? '#00ad9a' }};
            --secondary-color: {{ $tenant->secondary_color ?? '#333333' }};
            --tertiary-color: {{ $tenant->tertiary_color ?? '#ffffff' }};
        }
        
        .text-primary { color: var(--primary-color) !important; }
        .text-secondary { color: var(--secondary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
    </style>

    <div class="py-0 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 p-8 bg-white rounded-[2rem] border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-secondary">Olá, {{ $user->name }}!</h2>
                    <p class="text-gray-500 font-bold mt-1">Acompanhe seu progresso e suas missões.</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total de XP</span>
                    <p class="text-5xl font-black text-primary mt-1">{{ number_format($totalXp, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mb-10">
                <h3 class="text-xl font-black text-secondary mb-6 flex items-center">
                    <i class="fas fa-chalkboard-teacher mr-3 text-primary"></i> Minhas Turmas
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($user->classrooms as $classroom)
                        <div class="bg-white overflow-hidden shadow-sm rounded-[2rem] border border-gray-100 hover:border-primary/40 transition-all p-8 group">
                            <h4 class="text-xl font-black text-secondary mb-2">{{ $classroom->name }}</h4>
                            <p class="text-gray-400 font-bold text-sm mb-6">{{ $classroom->subject ?? 'Sem disciplina' }}</p>
                            
                            <a href="{{ route('student.classrooms.show', $classroom->id) }}" 
                               class="inline-flex items-center justify-center w-full px-6 py-3 bg-gray-50 border border-gray-200 text-gray-500 rounded-2xl font-black text-[11px] uppercase tracking-widest group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all">
                                Entrar na Sala
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 bg-white rounded-[2rem] border border-gray-100">
                            <p class="text-gray-400 font-bold">Você ainda não está matriculado em nenhuma turma.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm p-8 border border-gray-100">
                    <h3 class="text-xl font-black text-secondary mb-8 flex items-center">
                        <i class="far fa-calendar-alt mr-3 text-primary"></i> Minha Agenda
                    </h3>
                    <div id="calendar"></div>
                </div>

                <div class="lg:col-span-1 bg-white rounded-[2rem] shadow-sm p-8 border border-gray-100">
                    <h3 class="text-xl font-black text-secondary mb-8 flex items-center">
                        <i class="fas fa-rocket mr-3 text-primary"></i> Missões Abertas
                    </h3>
                    <div class="space-y-4">
                        @forelse($atividades as $atv)
                            <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100 hover:border-primary/40 transition-all group">
                                <span class="text-[9px] font-black uppercase text-primary bg-primary/10 px-3 py-1.5 rounded-xl">
                                    {{ $atv->classroom->name }}
                                </span>
                                <h4 class="font-black text-secondary text-sm mt-4 mb-5 leading-tight">{{ $atv->title }}</h4>
                                
                                <a href="{{ route('student.activities.show', $atv->id) }}" 
                                   class="flex items-center justify-center w-full py-3.5 bg-white border border-gray-200 rounded-2xl text-[10px] font-black text-gray-500 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all shadow-sm uppercase">
                                    Começar Tarefa
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="bg-emerald-50 text-emerald-500 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check text-xl"></i>
                                </div>
                                <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest">Nenhuma tarefa pendente!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                events: @json($eventos),
                eventDidMount: function(info) {
                    tippy(info.el, {
                        content: `
                            <div class="p-1 text-center">
                                <div class="text-[10px] uppercase opacity-50 mb-1 font-black">
                                    ${info.event.start.toLocaleDateString('pt-BR', { weekday: 'long', day: 'numeric', month: 'long' })}
                                </div>
                                <div class="text-sm font-black mb-1">${info.event.title}</div>
                                <hr class="border-white/10 my-2">
                                <div class="text-[11px] font-medium opacity-90">${info.event.extendedProps.description || ''}</div>
                            </div>
                        `,
                        allowHTML: true,
                        placement: 'top',
                        theme: 'light-border',
                        animation: 'shift-away'
                    });
                },
                height: 'auto',
                displayEventTime: false
            });
            calendar.render();
        });
    </script>

    <style>
        /* Ajustes FullCalendar */
        .fc-toolbar-title { font-weight: 900 !important; color: var(--secondary-color); text-transform: uppercase; font-size: 1.2rem !important; }
        .fc-button-primary { background-color: var(--primary-color) !important; border: none !important; font-weight: 900 !important; text-transform: uppercase; font-size: 10px !important; border-radius: 12px !important; padding: 8px 15px !important; }
        .fc-event { cursor: pointer; border: none !important; padding: 4px 8px !important; border-radius: 8px !important; font-weight: 700 !important; font-size: 11px !important; }
        .fc-daygrid-day-number { font-weight: 800; color: var(--secondary-color); text-decoration: none !important; padding: 10px !important; font-size: 0.8rem; }
        .fc-col-header-cell-cushion { color: #9CA3AF; font-size: 10px; font-weight: 800; text-transform: uppercase; text-decoration: none !important; }
        .fc-day-today { background: rgba(0, 173, 154, 0.03) !important; }
        
        /* Tippy Tooltip Custom */
        .tippy-box { background-color: var(--secondary-color); color: var(--tertiary-color); border-radius: 14px; padding: 8px; font-weight: bold; box-shadow: 0 15px 30px -5px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</x-app-layout>