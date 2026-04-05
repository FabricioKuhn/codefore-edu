<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
    ['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')],
    ['name' => 'Minhas Turmas', 'url' => route(auth()->user()->role . '.classrooms.index')],
    ['name' => $activity->classroom->name, 'url' => route(auth()->user()->role . '.classrooms.show', $activity->classroom)],
    ['name' => $activity->title, 'url' => '#']
]" />
        <div class="flex justify-between items-center" x-data>
            <h2 class="text-xl font-semibold text-secondary leading-tight">
                Atividade: {{ $activity->title }}
            </h2>
            <x-primary-button type="button" @click="$dispatch('open-create-modal')">Nova Questão</x-primary-button>
        </div>
    </x-slot>

    <div x-data="{ lightboxOpen: false, lightboxImg: '' }">
        <div class="py-12" x-data="questionEngine()" @open-create-modal.window="openForCreate()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-codeforce-green text-primary px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-6 bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-secondary">{{ $activity->title }}</h3>
                        <p class="text-gray-500 mt-2">{{ $activity->description ?? 'Sem descrição' }}</p>
                    </div>
                    <div class="mt-4 lg:mt-0 flex flex-col sm:flex-row items-center gap-6">
                        <div class="text-center bg-gray-100 p-4 rounded-lg flex flex-col gap-2 shadow-sm border border-gray-200">
                            <span class="text-[10px] uppercase text-gray-500 font-bold tracking-wider">XP Base</span>
                            <div class="text-2xl font-mono font-black text-primary">{{ $activity->base_xp }}</div>
                        </div>
                    </div>
                </div>

                <!-- Configurações Rápidas -->
                <form action="{{ route(auth()->user()->role . '.activities.update', $activity->id) }}" method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm flex flex-wrap gap-4 items-end">
                    @csrf @method('PUT')
                    
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Status</label>
                        <select name="status" class="block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm font-bold text-gray-700">
                            <option value="draft" {{ $activity->status == 'draft' ? 'selected' : '' }}>Rascunho</option>
                            <option value="active" {{ $activity->status == 'active' ? 'selected' : '' }}>Ativa</option>
                            <option value="closed" {{ $activity->status == 'closed' ? 'selected' : '' }}>Encerrada</option>
                            <option value="canceled" {{ $activity->status == 'canceled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Data Início</label>
                        <input type="datetime-local" name="start_date" value="{{ $activity->start_date ? \Carbon\Carbon::parse($activity->start_date)->format('Y-m-d\TH:i') : '' }}" class="block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                    </div>

                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Data Término</label>
                        <input type="datetime-local" name="end_date" value="{{ $activity->end_date ? \Carbon\Carbon::parse($activity->end_date)->format('Y-m-d\TH:i') : '' }}" class="block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                    </div>

                    <div class="flex-1 min-w-[120px]">
                        <label class="block text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Tempo (Min)</label>
                        <input type="number" name="duration_minutes" value="{{ $activity->duration_minutes }}" class="block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm" placeholder="Ilimitado">
                    </div>

                    <div class="flex-none flex items-center mb-1 px-2">
                        <label class="relative inline-flex items-center cursor-pointer" title="Embaralhar Respostas das Questões">
                            <input type="checkbox" name="shuffle_options" value="1" class="sr-only peer" {{ $activity->shuffle_options ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-2 text-[10px] font-bold text-gray-700 uppercase tracking-widest leading-tight">Múltipla Escolha<br>Embaralhada</span>
                        </label>
                    </div>

                    <div class="flex-none">
                        <x-primary-button class="h-[42px] px-6">Salvar</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="flex items-center justify-between mb-4 px-2">
                <h3 class="text-xl font-bold text-secondary">Questões da Atividade</h3>
            </div>
            
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 overflow-x-auto mb-8">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">Enunciado</th>
                            <th scope="col" class="px-6 py-3 text-center">Tipo</th>
                            <th scope="col" class="px-6 py-3 text-center">Peso</th>
                            <th scope="col" class="px-6 py-3 text-center">Anexo</th>
                            <th scope="col" class="px-6 py-3 text-center">Botão</th>
                            <th scope="col" class="px-6 py-3 text-center">Respostas</th>
                            <th scope="col" class="px-6 py-3 text-center">Status</th>
                            <th scope="col" class="px-6 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activity->questions as $index => $question)
                        <tr class="bg-white border-b hover:bg-gray-50 transition {{ $question->status ? '' : 'opacity-60' }}">
                            <td class="px-6 py-4 font-bold text-secondary">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium" title="{{ $question->statement }}">
                                {{ \Illuminate\Support\Str::limit($question->statement, 35) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">
                                    {{ $question->type === 'multiple_choice' ? 'Múltipla' : 'Descritiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-primary">{{ $question->weight }}</td>
                            <td class="px-6 py-4 text-center">
                                @php $hasImage = collect($question->attachments)->contains('type', 'image'); @endphp
                                @if($hasImage)
                                    <span class="text-green-600 font-bold">&#10003;</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php $hasBtn = collect($question->attachments)->contains('type', 'link_button'); @endphp
                                @if($hasBtn)
                                    <span class="text-green-600 font-bold">&#10003;</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-xs font-semibold text-primary">
                                {{ \App\Models\SubmissionAnswer::where('question_id', $question->id)->count() }} envios
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($question->status)
                                    <form action="{{ route(auth()->user()->role . '.questions.update_status', $question->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider hover:brightness-90 transition" title="Desabilitar">Ativa</button>
                                    </form>
                                @else
                                    <form action="{{ route(auth()->user()->role . '.questions.update_status', $question->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider hover:brightness-90 transition" title="Habilitar">Inativa</button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" @click.prevent="openForEdit({{ $question->toJson() }}, {{ $question->options->toJson() }})" class="text-white bg-primary px-3 py-1 rounded text-xs font-bold transition-all duration-200 hover:brightness-90 shadow-sm">Editar</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">Nenhuma questão cadastrada para esta atividade ainda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mb-4 px-2 mt-10">
                <h3 class="text-xl font-bold text-secondary">Alunos Vinculados</h3>
            </div>
            
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">Matrícula</th>
                            <th scope="col" class="px-6 py-3">Nome</th>
                            <th scope="col" class="px-6 py-3 text-center">Respondeu</th>
                            <th scope="col" class="px-6 py-3 text-center">Nota</th>
                            <th scope="col" class="px-6 py-3 text-center">Prazo</th>
                            <th scope="col" class="px-6 py-3 text-center">Status</th>
                            <th scope="col" class="px-6 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $disabledStudents = is_array($activity->disabled_students) ? $activity->disabled_students : json_decode($activity->disabled_students, true) ?? [];
                        @endphp
                        @forelse($activity->classroom->students ?? [] as $student)
                        @php
                            $isDisabled = in_array($student->id, $disabledStudents);
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50 transition {{ $isDisabled ? 'opacity-50' : '' }}">
                            <td class="px-6 py-4 text-secondary font-mono">{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 {{ $isDisabled ? 'line-through text-gray-400' : '' }}">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded shadow-sm text-[10px] uppercase font-bold">Não</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-primary">-</td>
                            <td class="px-6 py-4 text-center">{{ $activity->end_date ? \Carbon\Carbon::parse($activity->end_date)->format('d/m/Y') : 'Sem Prazo' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($isDisabled)
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">Desabilitado</span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">Habilitado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center flex justify-center gap-2">
                                <button type="button" class="text-white bg-primary px-3 py-1 rounded text-xs font-bold transition-all duration-200 hover:brightness-90 shadow-sm" title="Avaliar">Avaliar</button>
                                <button type="button" class="text-white bg-blue-500 px-3 py-1 rounded text-xs font-bold transition-all duration-200 hover:brightness-90 shadow-sm" title="Prorrogar Prazo">Prazo</button>
                                @if($isDisabled)
                                    <form action="{{ route(auth()->user()->role . '.activities.students.toggle', ['activity' => $activity->id, 'student' => $student->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-white bg-green-600 px-3 py-1 rounded text-xs font-bold transition-all duration-200 hover:brightness-90 shadow-sm" title="Habilitar">Habilitar</button>
                                    </form>
                                @else
                                    <form action="{{ route(auth()->user()->role . '.activities.students.toggle', ['activity' => $activity->id, 'student' => $student->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-white bg-red-500 px-3 py-1 rounded text-xs font-bold transition-all duration-200 hover:brightness-90 shadow-sm" title="Desabilitar">Ocultar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Nenhum aluno vinculado a esta turma.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Integrado (Criar / Editar) -->
        <div x-show="openModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="openModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-[800px] w-full">
                     
                    <form :action="actionUrl" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Simula PUT no Laravel caso method =='PUT' -->
                        <template x-if="method === 'PUT'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-secondary mb-4" id="modal-title" x-text="method === 'POST' ? 'Nova Questão' : 'Editar Questão'"></h3>

                            <!-- Tipo da Questão -->
                            <div class="mb-4">
                                <x-input-label for="type" value="Tipo de Questão" />
                                <select id="type" name="type" x-model="form.type" class="block mt-1 w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                                    <option value="multiple_choice">Múltipla Escolha</option>
                                    <option value="descriptive">Descritiva (Correção por IA)</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Campos de Mídia -->
                            <div class="mb-4 p-4 border border-dashed border-gray-300 rounded-md bg-gray-50">
                                <h4 class="text-sm font-bold text-gray-700 mb-3">Mídia Alternativa (Opcional)</h4>
                                
                                <div class="mb-3">
                                    <x-input-label for="images" value="Imagens da Questão (Permite Selecionar Várias)" />
                                    <!-- Add [] to name for multiple files -->
                                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700" @change="imagePreviews = []; Array.from($event.target.files).forEach(file => imagePreviews.push(URL.createObjectURL(file)))">
                                    <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                                    <div class="flex flex-wrap gap-2 mt-2" x-show="imagePreviews.length > 0">
                                        <template x-for="src in imagePreviews">
                                            <img :src="src" class="w-20 h-20 object-cover rounded-md border border-gray-300 shadow-sm" alt="Preview">
                                        </template>
                                        <template x-if="editing && currentQuestion.attachments">
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                <template x-for="(attachment, index) in currentQuestion.attachments.filter(a => a.type === 'image')" :key="index">
                                                    <div class="relative group">
                                                        <img :src="attachment.url" 
                                                            class="w-20 h-20 object-cover rounded-md border border-gray-300 shadow-sm opacity-60" 
                                                            alt="Imagem existente">
                                                        <span class="absolute top-1 right-1 text-xs bg-gray-600 text-white p-1 rounded-full leading-none">Salva</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="button_text" value="Texto do Botão de Link" />
                                        <x-text-input id="button_text" class="block mt-1 w-full" type="text" name="button_text" x-model="form.button_text" placeholder="Ex: Acessar Material" />
                                        <x-input-error :messages="$errors->get('button_text')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="button_url" value="URL do Botão (Abre em nova guia)" />
                                        <x-text-input id="button_url" class="block mt-1 w-full" type="url" name="button_url" x-model="form.button_url" placeholder="https://..." />
                                        <x-input-error :messages="$errors->get('button_url')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Enunciado -->
                            <div class="mb-4">
                                <x-input-label for="statement" value="Enunciado" />
                                <!-- Removed native required so browser won't silently block -->
                                <textarea id="statement" name="statement" x-model="form.statement" class="block mt-1 w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm" rows="3"></textarea>
                                <x-input-error :messages="$errors->get('statement')" class="mt-2" />
                            </div>

                            <!-- Peso -->
                            <div class="mb-4 w-full md:w-1/3">
                                <x-input-label for="weight" value="Peso da Questão" />
                                <!-- Removed native required -->
                                <x-text-input id="weight" class="block mt-1 w-full" type="number" name="weight" x-model="form.weight" min="1" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                            </div>

                            <!-- Bloco Múltipla Escolha -->
                            <div x-show="form.type === 'multiple_choice'" class="mt-4 border border-gray-200 rounded-md p-4 bg-gray-50">
                                <h4 class="font-bold text-secondary mb-4">Alternativas (Marque a correta)</h4>
                                
                                <template x-for="(opt, index) in [0, 1, 2, 3]" :key="index">
                                    <div class="flex items-center gap-4 mb-3">
                                        <input type="radio" name="correct_option" :value="index" x-model="form.correct_option" class="h-4 w-4 text-primary focus:ring-primary border-gray-300">
                                        <x-text-input class="block w-full" type="text" x-bind:name="'options['+index+']'" x-model="form.options[index]" placeholder="Alternativa" />
                                    </div>
                                </template>
                                <x-input-error :messages="$errors->get('options')" class="mt-2" />
                                <x-input-error :messages="$errors->get('correct_option')" class="mt-2" />
                            </div>

                            <!-- Bloco Descritiva -->
                            <div x-cloak x-show="form.type === 'descriptive'" class="mt-4 border border-gray-200 rounded-md p-4 bg-gray-50">
                                <x-input-label for="expected_answer" value="Gabarito Esperado (Para a IA Corrigir)" />
                                <!-- Removed native required -->
                                <textarea id="expected_answer" name="expected_answer" x-model="form.expected_answer" class="block mt-1 w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm" rows="4"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Este texto será base para testes IA.</p>
                                <x-input-error :messages="$errors->get('expected_answer')" class="mt-2" />
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                            <x-primary-button class="w-full sm:ml-3 sm:w-auto">
                                Salvar Questão
                            </x-primary-button>
                            <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:max-w-4xl w-full sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lightbox Component -->
        <div x-show="lightboxOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm" @click="lightboxOpen = false" x-transition.opacity>
            <img :src="lightboxImg" class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl" @click.stop>
            <button type="button" @click="lightboxOpen = false" class="absolute top-4 right-4 text-white text-3xl font-bold">&times;</button>
        </div>
    </div>

    <!-- Script de gerencialmento de estado do formulário via Alpine -->
    <script>
        function questionEngine() {
            return {
                openModal: false,
                imagePreviews: [],
                actionUrl: '{{ route(auth()->user()->role . '.activities.questions.store', $activity) }}',
                method: 'POST',
                form: {
                    type: 'multiple_choice',
                    statement: '',
                    weight: 1,
                    expected_answer: '',
                    options: ['', '', '', ''],
                    correct_option: 0,
                    button_text: '',
                    button_url: ''
                },
                init() {
                    @if($errors->any())
                        this.openModal = true;
                        this.form.type = {!! json_encode(old('type', 'multiple_choice')) !!};
                        this.form.statement = {!! json_encode(old('statement', '')) !!};
                        this.form.weight = {!! json_encode(old('weight', 1)) !!};
                        this.form.expected_answer = {!! json_encode(old('expected_answer', '')) !!};
                        this.form.options = {!! json_encode(old('options', ['', '', '', ''])) !!};
                        this.form.correct_option = {!! json_encode(old('correct_option', 0)) !!};
                        this.form.button_text = {!! json_encode(old('button_text', '')) !!};
                        this.form.button_url = {!! json_encode(old('button_url', '')) !!};
                    @endif
                },
                openForCreate() {
                    this.method = 'POST';
                    this.actionUrl = '{{ route(auth()->user()->role . '.activities.questions.store', $activity) }}';
                    this.imagePreviews = [];
                    this.form = {
                        type: 'multiple_choice',
                        statement: '',
                        weight: 1,
                        expected_answer: '',
                        options: ['', '', '', ''],
                        correct_option: 0,
                        button_text: '',
                        button_url: ''
                    };
                    this.openModal = true;
                },
                openForEdit(q, relationOptions) {
                    this.method = 'PUT';
                    this.actionUrl = '/questions/' + q.id;
                    this.imagePreviews = [];
                    this.form.type = q.type;
                    this.form.statement = q.statement;
                    this.form.weight = q.weight;
                    this.form.expected_answer = q.expected_answer || '';
                    
                    if (relationOptions && relationOptions.length > 0) {
                        // Resgata o Array exato salvo
                        this.form.options = relationOptions.map(o => o.content);
                        let correctIdx = relationOptions.findIndex(o => o.is_correct);
                        this.form.correct_option = correctIdx !== -1 ? correctIdx : 0;
                    } else {
                        this.form.options = ['', '', '', ''];
                        this.form.correct_option = 0;
                    }

                    this.form.button_text = '';
                    this.form.button_url = '';

                    this.openModal = true;
                }
            }
        }
    </script>
</x-app-layout>
