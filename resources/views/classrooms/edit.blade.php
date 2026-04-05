<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')],
            ['name' => 'Minhas Turmas', 'url' => route(auth()->user()->role . '.classrooms.index')],
            ['name' => 'Editar Turma', 'url' => '#']
        ]" />
        <h2 class="text-xl font-semibold text-secondary leading-tight mt-2">
            Editando Turma: <span class="text-primary">{{ $classroom->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <form action="{{ route(auth()->user()->role . '.classrooms.update', $classroom) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-8 space-y-8">
                    
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-secondary">Dados da Turma</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <x-input-label for="name" value="Nome da Turma *" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $classroom->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="subject" value="Matéria / Disciplina *" />
                                <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" :value="old('subject', $classroom->subject)" required />
                                <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                            </div>

                            <div class="mt-4">
    <x-input-label for="teacher_id" value="Professor Responsável" />
    <select name="teacher_id" id="teacher_id" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-primary focus:ring-primary">
        <option value="">Selecione um professor</option>
        @foreach($teachers as $teacher)
            <option value="{{ $teacher->id }}" {{ (isset($classroom) && $classroom->teacher_id == $teacher->id) ? 'selected' : '' }}>
                {{ $teacher->name }}
            </option>
        @endforeach
    </select>
</div>

                        </div>
                    </section>

                    <section class="mt-8">
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-secondary">Calendário e Frequência</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            <div>
                                <x-input-label for="total_lessons" value="Total de Aulas do Curso *" />
                                <x-text-input id="total_lessons" name="total_lessons" type="number" class="mt-1 block w-full" :value="old('total_lessons', $classroom->total_lessons ?? 24)" required />
                            </div>

                            <div>
                                <x-input-label for="start_date" value="Data de Início *" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', (isset($classroom) && $classroom->start_date) ? $classroom->start_date->format('Y-m-d') : '')" required />
                            </div>

                            <div>
                                <x-input-label for="min_attendance_percent" value="% Mínima para Aprovação" />
                                <x-text-input id="min_attendance_percent" name="min_attendance_percent" type="number" step="0.1" class="mt-1 block w-full" :value="old('min_attendance_percent', $classroom->min_attendance_percent ?? 70)" />
                            </div>

                            <div>
                                <x-input-label for="frequency" value="Frequência das Aulas" />
                                <select name="frequency" id="frequency" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                                    <option value="weekly" {{ old('frequency', $classroom->frequency ?? '') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                    <option value="biweekly" {{ old('frequency', $classroom->frequency ?? '') == 'biweekly' ? 'selected' : '' }}>Quinzenal</option>
                                    <option value="daily" {{ old('frequency', $classroom->frequency ?? '') == 'daily' ? 'selected' : '' }}>Diário (Seg a Sex)</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label value="Dias da Semana" />
                                <div class="flex flex-wrap gap-4 mt-3">
                                    @php $selectedDays = old('days_of_week', $classroom->days_of_week ?? []); @endphp
                                    @foreach(['1'=>'Seg', '2'=>'Ter', '3'=>'Qua', '4'=>'Qui', '5'=>'Sex', '6'=>'Sáb', '0'=>'Dom'] as $val => $label)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="days_of_week[]" value="{{ $val }}" 
                                                {{ in_array($val, (array) $selectedDays) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
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
                                    <input type="checkbox" name="skip_holidays" value="1" {{ old('skip_holidays', $classroom->skip_holidays ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-600">Pular Feriados</span>
                                </label>
                            </div>
                        </div>
                    </section>

                    <div class="pt-6 flex justify-end gap-4 border-t border-gray-200">
                        <a href="{{ route(auth()->user()->role . '.classrooms.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancelar
                        </a>
                        <x-primary-button>
                            Salvar Alterações
                        </x-primary-button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>