<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
            ['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')],
            ['name' => 'Banco de Questões', 'url' => route(auth()->user()->role . '.questions.index')],
            ['name' => 'Editar Questão', 'url' => '#']
        ]" />
        <div class="flex justify-between items-center mt-2">
            <h2 class="text-xl font-bold text-secondary leading-tight">
                Editar Questão #{{ $question->id }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="questionForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-bold">
                    ⚠️ Atenção: Alguns campos não foram preenchidos corretamente. Verifique abaixo.
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route(auth()->user()->role . '.questions.update', $question) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-b border-gray-100 pb-8">
                            <div>
                                <x-input-label for="type" value="Tipo de Questão" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                                <select id="type" name="type" x-model="form.type" class="block w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm font-bold text-secondary">
                                    <option value="multiple_choice">Múltipla Escolha</option>
                                    <option value="descriptive">Descritiva (Aberta)</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="default_weight" value="Peso Padrão" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                                <x-text-input id="default_weight" class="block w-full rounded-xl border-gray-200" type="number" name="default_weight" x-model="form.weight" min="1" />
                            </div>
                        </div>

                        <div class="mb-8">
                            <x-input-label for="statement" value="Enunciado da Questão" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                            <textarea id="statement" name="statement" x-model="form.statement" class="block w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm" rows="5"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 p-8 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <div>
                                <x-input-label value="Imagens de Apoio" class="text-[10px] font-black uppercase text-gray-400 mb-3" />
                                <input type="file" name="attachments[]" multiple class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-secondary file:text-white hover:file:brightness-110" />
                                
                                @if($question->attachments)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($question->attachments as $img)
                                            <img src="{{ asset('storage/'.$img) }}" class="w-14 h-14 object-cover rounded-lg border-2 border-white shadow-sm">
                                        @endforeach
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase italic">Imagens já salvas acima.</p>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label value="Link Externo (URL)" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <x-text-input name="external_link" x-model="form.external_link" placeholder="https://..." class="w-full text-sm" />
                                </div>
                                <div>
                                    <x-input-label value="Texto do Botão" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <x-text-input name="external_link_label" x-model="form.external_link_label" placeholder="Ex: Ver Vídeo" class="w-full text-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-8 border-b border-gray-100 pb-8">
                            <x-input-label for="guidelines" value="Dicas / Orientações para o Aluno" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                            <textarea id="guidelines" name="guidelines" x-model="form.guidelines" class="block w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm bg-white" rows="3"></textarea>
                        </div>

                        <div x-show="form.type === 'multiple_choice'" x-cloak class="bg-blue-50/50 border border-blue-100 rounded-xl p-6 mb-8">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="font-bold text-secondary text-lg">Alternativas</h4>
                                <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200 shadow-sm">Selecione a correta</span>
                            </div>
                            
                            <div class="space-y-3">
                                <template x-for="(opt, index) in options" :key="index">
                                    <div class="flex items-center gap-4 bg-white p-2 rounded-xl border border-gray-200 shadow-sm transition-all group hover:border-primary">
                                        <div class="pl-3">
                                            <input type="radio" name="correct_option" :value="index" x-model="correctOption" class="h-5 w-5 text-primary focus:ring-primary border-gray-300 cursor-pointer">
                                        </div>
                                        <input type="text" :name="'options['+index+'][content]'" x-model="options[index]" class="block w-full border-0 focus:ring-0 text-sm p-2 bg-transparent font-medium text-gray-700" :placeholder="'Alternativa ' + (index + 1)">
                                        <button type="button" @click="removeOption(index)" x-show="options.length > 2" class="pr-4 text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            
                            <div class="mt-6 flex justify-center">
                                <button type="button" @click="addOption()" class="flex items-center gap-2 px-4 py-2 bg-white border border-dashed border-gray-300 text-gray-600 rounded-full text-xs font-bold uppercase tracking-widest hover:border-primary hover:text-primary transition-colors">
                                    + Adicionar Alternativa
                                </button>
                            </div>
                        </div>

                        <div x-show="form.type === 'descriptive'" x-cloak class="bg-purple-50/50 border border-purple-100 rounded-xl p-6 mb-8">
                            <h4 class="font-bold text-secondary text-lg mb-4">Gabarito Esperado</h4>
                            <textarea id="expected_answer" name="expected_answer" x-model="form.expected_answer" class="block w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl shadow-sm text-sm" rows="6"></textarea>
                        </div>

                        <div x-data="tagManager()" class="mb-6 pt-8 border-t border-gray-100">
                            <x-input-label value="Tags / Categorias" class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-2" />
                            <div class="flex flex-wrap items-center gap-2 block w-full border border-gray-200 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary rounded-xl shadow-sm text-sm p-3 bg-white">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="bg-primary/10 text-primary px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1 border border-primary/20 transition-all">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(index)" class="hover:text-red-500 font-bold ml-1 text-xs">&times;</button>
                                    </span>
                                </template>
                                <input type="text" x-model="newTag" @keydown.enter.prevent="addTag()" @keydown.comma.prevent="addTag()" placeholder="Adicionar tag..." class="border-0 focus:ring-0 p-0 flex-1 text-sm bg-transparent min-w-[120px]">
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
                            Atualizar Questão
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        // Preparação das opções para o Alpine
        if (old('options')) {
            $jsOptions = json_encode(array_map(function($o) { return $o['content'] ?? $o; }, old('options')));
            $correctIdx = old('correct_option', 0);
        } else {
            if ($question->type === 'multiple_choice' && $question->options->count() > 0) {
                $jsOptions = $question->options->pluck('content')->toJson();
                $correctIdx = $question->options->search(function($o) { return $o->is_correct; }) ?: 0;
            } else {
                $jsOptions = json_encode(['', '', '', '']);
                $correctIdx = 0;
            }
        }
    @endphp

    <script>
        function tagManager() {
            return {
                newTag: '',
                tags: {!! json_encode(old('tags', $question->tags ?? [])) !!},
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
            }
        }

        function questionForm() {
            return {
                form: {
                    type: '{!! old('type', $question->type) !!}',
                    statement: {!! json_encode(old('statement', $question->statement)) !!},
                    weight: {!! old('default_weight', $question->default_weight) !!},
                    guidelines: {!! json_encode(old('guidelines', $question->guidelines)) !!},
                    expected_answer: {!! json_encode(old('expected_answer', $question->expected_answer)) !!},
                    external_link: '{!! old('external_link', $question->external_link) !!}',
                    external_link_label: '{!! old('external_link_label', $question->external_link_label) !!}',
                },
                
                options: {!! $jsOptions !!},
                correctOption: {{ $correctIdx }},

                addOption() {
                    this.options.push('');
                },

                removeOption(index) {
                    if(this.options.length > 2) {
                        this.options.splice(index, 1);
                        if(this.correctOption == index || this.correctOption >= this.options.length) {
                            this.correctOption = 0;
                        } else if (this.correctOption > index) {
                            this.correctOption--;
                        }
                    }
                }
            }
        }
    </script>
</x-app-layout>