<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')],
            ['name' => 'Banco de Questões', 'url' => route(auth()->user()->role . '.questions.index')],
            ['name' => 'Nova Questão', 'url' => '#']
        ]" />
        <div class="flex justify-between items-center mt-2">
            <h2 class="text-xl font-bold text-secondary leading-tight">
                Adicionar Nova Questão ao Banco
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="questionForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route(auth()->user()->role . '.questions.store') }}" method="POST" enctype="multipart/form-data">
                    @if ($errors->any())
                        <div class="mx-8 mt-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-bold">
                            ⚠️ Atenção: Alguns campos obrigatórios não foram preenchidos corretamente. Verifique abaixo.
                        </div>
                    @endif
                    @csrf

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-b border-gray-100 pb-8">
                            <div>
                                <x-input-label for="type" value="Tipo de Questão" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                                <select id="type" name="type" x-model="form.type" class="block w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm font-bold text-secondary">
                                    <option value="multiple_choice">Múltipla Escolha</option>
                                    <option value="descriptive">Descritiva (Aberta)</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="default_weight" value="Peso Padrão da Questão" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                                <x-text-input id="default_weight" class="block w-full rounded-xl border-gray-200" type="number" name="default_weight" x-model="form.weight" min="1" />
                                <p class="text-xs text-gray-400 mt-2">Pode ser alterado depois dentro da prova.</p>
                                <x-input-error :messages="$errors->get('default_weight')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-8 border-b border-gray-100 pb-8">
                            <div class="mb-6">
                                <x-input-label for="statement" value="Enunciado da Questão" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                                <textarea id="statement" name="statement" x-model="form.statement" class="block w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm" rows="5" placeholder="Digite a pergunta ou o problema aqui..."></textarea>
                                <x-input-error :messages="$errors->get('statement')" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <x-input-label for="guidelines" value="Orientações / Dicas (Opcional)" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                                <textarea id="guidelines" name="guidelines" x-model="form.guidelines" class="block w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm bg-gray-50" rows="2" placeholder="Ex: Leia atentamente o texto base antes de responder..."></textarea>
                                <x-input-error :messages="$errors->get('guidelines')" class="mt-2" />
                            </div>

                            <div class="p-6 border border-dashed border-gray-300 rounded-xl bg-gray-50">
                                <h4 class="text-[10px] uppercase font-black tracking-widest text-gray-500 mb-4">Anexos e Material de Apoio (Opcional)</h4>
                                
                                <div class="mb-5">
                                    <x-input-label for="images" value="Imagens da Questão" class="mb-2" />
                                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:uppercase file:font-black file:tracking-widest file:bg-secondary file:text-white hover:file:bg-gray-800 transition-all cursor-pointer" @change="handleFiles">
                                    <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                                    
                                    <div class="flex flex-wrap gap-3 mt-4" x-show="imagePreviews.length > 0" x-cloak>
                                        <template x-for="src in imagePreviews">
                                            <img :src="src" class="w-24 h-24 object-cover rounded-xl border border-gray-200 shadow-sm" alt="Preview">
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="button_text" value="Texto do Botão de Link" />
                                        <x-text-input id="button_text" class="block mt-1 w-full rounded-xl border-gray-200" type="text" name="button_text" x-model="form.button_text" placeholder="Ex: Acessar Artigo Completo" />
                                    </div>
                                    <div>
                                        <x-input-label for="button_url" value="URL do Botão" />
                                        <x-text-input id="button_url" class="block mt-1 w-full rounded-xl border-gray-200" type="url" name="button_url" x-model="form.button_url" placeholder="https://..." />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="form.type === 'multiple_choice'" x-cloak class="bg-blue-50/50 border border-blue-100 rounded-xl p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="font-bold text-secondary text-lg">Alternativas da Questão</h4>
                                <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200 shadow-sm">Selecione a correta no círculo</span>
                            </div>
                            
                            <div class="space-y-3">
                                <template x-for="(opt, index) in options" :key="index">
                                    <div class="flex items-center gap-4 bg-white p-2 rounded-xl border border-gray-200 shadow-sm transition-all group hover:border-primary">
                                        <div class="pl-3">
                                            <input type="radio" name="correct_option" :value="index" x-model="correctOption" class="h-5 w-5 text-primary focus:ring-primary border-gray-300 cursor-pointer">
                                        </div>
                                        
                                        <input type="text" :name="'options['+index+'][content]'" x-model="options[index]" class="block w-full border-0 focus:ring-0 text-sm p-2 bg-transparent font-medium text-gray-700 placeholder-gray-300" :placeholder="'Alternativa ' + (index + 1)">
                                        
                                        <button type="button" @click="removeOption(index)" x-show="options.length > 2" class="pr-4 text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            
                            <x-input-error :messages="$errors->get('options')" class="mt-2" />
                            <x-input-error :messages="$errors->get('correct_option')" class="mt-2" />

                            <div class="mt-6 flex justify-center">
                                <button type="button" @click="addOption()" class="flex items-center gap-2 px-4 py-2 bg-white border border-dashed border-gray-300 text-gray-600 rounded-full text-xs font-bold uppercase tracking-widest hover:border-primary hover:text-primary transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Adicionar Alternativa
                                </button>
                            </div>
                        </div>

                        <div x-show="form.type === 'descriptive'" x-cloak class="bg-purple-50/50 border border-purple-100 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <h4 class="font-bold text-secondary text-lg">Gabarito Esperado</h4>
                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-[9px] font-black uppercase tracking-wider">Uso Interno</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">Este texto servirá como base para o professor corrigir a prova (e para a IA fazer sugestões no futuro).</p>
                            
                            <textarea id="expected_answer" name="expected_answer" x-model="form.expected_answer" class="block w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl shadow-sm text-sm" rows="6" placeholder="Escreva aqui a resposta modelo..."></textarea>
                            <x-input-error :messages="$errors->get('expected_answer')" class="mt-2" />
                        </div>

                    

                    <div class="mb-6 pt-8" x-data="{ 
    newTag: '', 
    tags: {{ json_encode(old('tags', [])) }},
    addTag() {
        let t = this.newTag.trim().toUpperCase();
        if (t !== '' && !this.tags.includes(t)) {
            this.tags.push(t);
        }
        this.newTag = '';
    },
    removeTag(index) {
        this.tags.splice(index, 1);
    }
}">
    <x-input-label value="Tags / Categorias (Ex: MATEMÁTICA, FRAÇÕES, DIFÍCIL)" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
    
    <div class="flex flex-wrap items-center gap-2 block w-full border border-gray-200 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary rounded-xl shadow-sm text-sm p-3 bg-white">
        <template x-for="(tag, index) in tags" :key="index">
            <span class="bg-primary/10 text-primary px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1 border border-primary/20">
                <span x-text="tag"></span>
                <button type="button" @click="removeTag(index)" class="hover:text-red-500 font-bold ml-1">&times;</button>
            </span>
        </template>
        
        <input type="text" x-model="newTag" @keydown.enter.prevent="addTag()" @keydown.comma.prevent="addTag()" placeholder="Adicione tags..." class="border-0 focus:ring-0 p-0 flex-1 text-sm bg-transparent min-w-[120px]">
    </div>

    <template x-for="(tag, index) in tags" :key="index">
        <input type="hidden" name="tags[]" :value="tag">
    </template>
</div>

</div>

                    <div class="bg-gray-50 px-8 py-5 border-t border-gray-200 flex justify-end gap-3 items-center">
                        <a href="{{ route(auth()->user()->role . '.questions.index') }}" class="text-[10px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition px-4 py-2">
                            Cancelar
                        </a>
                        <x-primary-button class="px-8 py-3 text-sm">
                            Salvar Questão no Banco
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function questionForm() {
            return {
                imagePreviews: [],
                
                // Mapeia os dados velhos (old) caso a validação do Laravel falhe
                form: {
                    type: '{!! old('type', 'multiple_choice') !!}',
                    statement: {!! json_encode(old('statement', '')) !!},
                    weight: {!! old('default_weight', 1) !!},
                    guidelines: {!! json_encode(old('guidelines', '')) !!},
                    expected_answer: {!! json_encode(old('expected_answer', '')) !!},
                    button_text: {!! json_encode(old('button_text', '')) !!},
                    button_url: {!! json_encode(old('button_url', '')) !!}
                },
                
                // Controle dinâmico das alternativas
                options: {!! json_encode(old('options', [['content' => ''], ['content' => ''], ['content' => ''], ['content' => '']])) !!}.map(opt => typeof opt === 'string' ? opt : opt.content || ''),
                correctOption: {!! old('correct_option', 0) !!},

                addOption() {
                    this.options.push(''); // Adiciona uma alternativa vazia
                },

                removeOption(index) {
                    if(this.options.length > 2) {
                        this.options.splice(index, 1);
                        
                        // Ajusta o rádio button se a alternativa apagada for a correta ou antes da correta
                        if(this.correctOption == index || this.correctOption >= this.options.length) {
                            this.correctOption = 0;
                        } else if (this.correctOption > index) {
                            this.correctOption--;
                        }
                    }
                },

                handleFiles(event) {
                    this.imagePreviews = [];
                    Array.from(event.target.files).forEach(file => {
                        this.imagePreviews.push(URL.createObjectURL(file));
                    });
                }
            }
        }
    </script>
</x-app-layout>