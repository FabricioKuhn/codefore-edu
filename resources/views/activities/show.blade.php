<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
    ['name' => 'Home', 'url' => route('dashboard')],
    ['name' => 'Minhas Turmas', 'url' => route('classrooms.index')],
    ['name' => $activity->classroom->name, 'url' => route('classrooms.show', $activity->classroom)],
    ['name' => $activity->title, 'url' => '#']
]" />
        <div class="flex justify-between items-center" x-data>
            <h2 class="text-xl font-semibold text-[#333333] leading-tight">
                Missão: {{ $activity->title }}
            </h2>
            <x-primary-button type="button" @click="$dispatch('open-create-modal')">Nova Questão</x-primary-button>
        </div>
    </x-slot>

    <div x-data="{ lightboxOpen: false, lightboxImg: '' }">
        <div class="py-12" x-data="questionEngine()" @open-create-modal.window="openForCreate()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-codeforce-green text-codeforce-green px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-6 bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-[#333333]">{{ $activity->title }}</h3>
                    <p class="text-gray-500 mt-2">{{ $activity->description ?? 'Sem descrição' }}</p>
                </div>
                <div class="mt-4 md:mt-0 text-center bg-gray-100 p-4 rounded-lg flex gap-6">
                    <div>
                        <span class="text-sm uppercase text-gray-500 font-semibold tracking-wider">XP Base</span>
                        <div class="text-2xl font-mono font-bold mt-1 text-codeforce-green">{{ $activity->base_xp }}</div>
                    </div>
                    <div>
                        <span class="text-sm uppercase text-gray-500 font-semibold tracking-wider">Status</span>
                        <div class="text-xl font-bold mt-1 text-gray-700">{{ ucfirst($activity->status) }}</div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mb-4 px-2">
                <h3 class="text-xl font-bold text-[#333333]">Questões da Missão</h3>
            </div>
            
            <div class="space-y-4">
                @forelse ($activity->questions as $index => $question)
                    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 flex flex-col md:flex-row items-start md:items-center justify-between">
                        <div class="flex-1 w-full">
                            <h4 class="text-lg font-bold mb-1 text-[#333333]">Questão {{ $index + 1 }}</h4>

                            @if($question->attachments)
                                <div class="mb-4 mt-2 flex flex-wrap gap-3 items-center">
                                    @foreach($question->attachments as $attachment)
                                        @if($attachment['type'] === 'image')
                                            <img src="{{ $attachment['url'] }}" 
                                                class="w-20 h-20 object-cover rounded-md border border-gray-200 shadow-sm cursor-pointer hover:opacity-90 transition-opacity" 
                                                @click="lightboxImg = '{{ $attachment['url'] }}'; lightboxOpen = true" 
                                                alt="Anexo da questão">
                                                
                                        @elseif($attachment['type'] === 'link_button')
                                            <a href="{{ $attachment['url'] }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-[#333333] uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                                {{ $attachment['text'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>  
                            @endif

                            <p class="text-gray-700 mb-2">{{ $question->statement }}</p>
                            
                            <div class="flex items-center gap-4 text-sm mt-3">
                                <span class="bg-gray-100 text-codeforce-gray font-semibold px-2 py-1 rounded">Tipo: {{ $question->type === 'multiple_choice' ? 'Múltipla Escolha' : 'Descritiva' }}</span>
                                <span class="bg-teal-100 text-codeforce-green font-semibold px-2 py-1 rounded">Peso: {{ $question->weight }}</span>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-6 flex-shrink-0">
                             <a href="#" @click.prevent="openForEdit({{ $question->toJson() }}, {{ $question->options->toJson() }})" class="text-codeforce-green hover:text-[#008f7f] font-semibold text-sm">Editar</a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500 border border-gray-100">
                        Nenhuma questão cadastrada para esta missão ainda.
                    </div>
                @endforelse
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
                            <h3 class="text-lg leading-6 font-medium text-[#333333] mb-4" id="modal-title" x-text="method === 'POST' ? 'Nova Questão' : 'Editar Questão'"></h3>

                            <!-- Tipo da Questão -->
                            <div class="mb-4">
                                <x-input-label for="type" value="Tipo de Questão" />
                                <select id="type" name="type" x-model="form.type" class="block mt-1 w-full border-gray-300 focus:border-codeforce-green focus:ring-codeforce-green rounded-md shadow-sm text-sm">
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
                                <textarea id="statement" name="statement" x-model="form.statement" class="block mt-1 w-full border-gray-300 focus:border-codeforce-green focus:ring-codeforce-green rounded-md shadow-sm" rows="3"></textarea>
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
                                <h4 class="font-bold text-[#333333] mb-4">Alternativas (Marque a correta)</h4>
                                
                                <template x-for="(opt, index) in [0, 1, 2, 3]" :key="index">
                                    <div class="flex items-center gap-4 mb-3">
                                        <input type="radio" name="correct_option" :value="index" x-model="form.correct_option" class="h-4 w-4 text-codeforce-green focus:ring-codeforce-green border-gray-300">
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
                                <textarea id="expected_answer" name="expected_answer" x-model="form.expected_answer" class="block mt-1 w-full border-gray-300 focus:border-codeforce-green focus:ring-codeforce-green rounded-md shadow-sm" rows="4"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Este texto será base para testes IA.</p>
                                <x-input-error :messages="$errors->get('expected_answer')" class="mt-2" />
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                            <x-primary-button class="w-full sm:ml-3 sm:w-auto">
                                Salvar Questão
                            </x-primary-button>
                            <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-codeforce-green sm:mt-0 sm:ml-3 sm:max-w-4xl w-full sm:text-sm">
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
                actionUrl: '{{ route("activities.questions.store", $activity) }}',
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
                    this.actionUrl = '{{ route("activities.questions.store", $activity) }}';
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
