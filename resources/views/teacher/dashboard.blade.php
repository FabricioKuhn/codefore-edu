<x-app-layout>
    <style>
        :root {
            --primary-color: {{ $tenant->primary_color ?? '#00ad9a' }};
            --secondary-color: {{ $tenant->secondary_color ?? '#333333' }};
            --tertiary-color: {{ $tenant->tertiary_color ?? '#ffffff' }};
        }

        /* Helpers de Cores */
        .text-primary { color: var(--primary-color) !important; }
        .text-secondary { color: var(--secondary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-secondary { background-color: var(--secondary-color) !important; }

        /* Estilização FullCalendar */
        .fc-event { 
            cursor: pointer; 
            border: none !important; 
            padding: 5px 10px !important; 
            border-radius: 10px !important; 
            font-weight: 700 !important;
            font-size: 11px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .fc-toolbar-title { 
            color: var(--secondary-color) !important; 
            font-weight: 900 !important; 
            text-transform: uppercase; 
            font-size: 1.2rem !important; 
        }
        .fc-button-primary { 
            background-color: var(--primary-color) !important; 
            border-color: var(--primary-color) !important; 
            font-weight: 900 !important; 
            text-transform: uppercase;
            font-size: 10px !important;
            border-radius: 12px !important;
            padding: 8px 15px !important;
        }
        .fc-button-primary:disabled { opacity: 0.5; }
        .fc-day-today { background: rgba(0, 173, 154, 0.03) !important; }
        .fc-col-header-cell-cushion { color: #9CA3AF; font-size: 10px; font-weight: 800; text-transform: uppercase; text-decoration: none !important; }
        .fc-daygrid-day-number { color: var(--secondary-color); font-weight: 800; text-decoration: none !important; padding: 10px !important; }

        /* Estilo Customizado do Tooltip (Tippy) */
        .tippy-box { 
            background-color: var(--secondary-color); 
            color: var(--tertiary-color); 
            border-radius: 14px; 
            padding: 8px; 
            font-weight: bold;
            box-shadow: 0 15px 30px -5px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }
    </style>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

    <div class="py-0 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(auth()->user()->role === 'student')
                {{-- Conteúdo Aluno --}}
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="bg-white rounded-[2rem] shadow-sm p-7 border border-gray-100">
                        <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Turmas Ativas</h3>
                        <p class="text-4xl font-black text-secondary mt-1">{{ $totalTurmas }}</p>
                    </div>
                    <div class="bg-white rounded-[2rem] shadow-sm p-7 border border-gray-100">
                        <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Total Alunos</h3>
                        <p class="text-4xl font-black text-secondary mt-1">{{ $totalAlunos }}</p>
                    </div>
                    <div class="bg-white rounded-[2rem] shadow-sm p-7 border border-gray-100">
                        <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest">XP Gerada</h3>
                        <p class="text-4xl font-black text-primary mt-1">+{{ number_format($xpGerada, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white rounded-[2rem] shadow-sm p-7 border border-gray-100">
                        <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Novos Alunos</h3>
                        <p class="text-4xl font-black text-secondary mt-1">{{ $novosAlunos }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm p-8 border border-gray-100">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-black text-secondary flex items-center">
                                <i class="far fa-calendar-alt mr-3 text-primary"></i> Agenda Geral
                            </h3>
                        </div>
                        <div id="calendar" class="min-h-[600px]"></div>
                    </div>

                    @if(auth()->user()->role === 'teacher')
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-[2.5rem] shadow-sm p-8 border border-gray-100">
                            <h3 class="text-xl font-black text-secondary mb-8 flex items-center">
                                <i class="fas fa-clipboard-check mr-3 text-primary"></i> Por Corrigir
                            </h3>
                            <div class="space-y-4">
                                @forelse($correcoesPendentes as $atv)
                                    <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100 hover:border-primary/40 transition-all group">
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="text-[9px] font-black uppercase text-primary bg-primary/10 px-3 py-1.5 rounded-xl">
                                                {{ $atv->classroom->name }}
                                            </span>
                                            <span class="bg-secondary text-white text-[10px] font-black px-2.5 py-1 rounded-lg">
                                                {{ $atv->submissions_count }}
                                            </span>
                                        </div>
                                        <h4 class="font-black text-secondary text-sm mb-5 leading-tight uppercase">{{ $atv->title }}</h4>
                                        <a href="{{ route('teacher.submissions.index', $atv->id) }}" 
                                           class="flex items-center justify-center w-full py-3.5 bg-white border border-gray-200 rounded-2xl text-[10px] font-black text-gray-500 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-sm">
                                            ABRIR CORREÇÕES
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-center text-gray-400 font-bold py-10 uppercase text-[10px] tracking-widest">Nada para corrigir!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: '' // ✅ Botões Mês/Agenda removidos conforme solicitado
                },
                events: @json($eventos),
                
                // ✅ CONFIGURAÇÃO DO TOOLTIP PARA NÃO CORTAR TEXTO
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
                displayEventTime: false,
            });
            calendar.render();
        });
    </script>
</x-app-layout>