<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
    ['name' => 'Home', 'url' => route('dashboard')], 
    ['name' => 'Secretaria de Alunos', 'url' => route('students.index')],
    ['name' => 'Editar Aluno', 'url' => '#']
]" />
        <h2 class="text-xl font-semibold text-[#333333] leading-tight mt-2">
            Editando Dados de: <span class="text-codeforce-green">{{ $student->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-8 space-y-8">
                    
                    <!-- Seção 1: Acesso -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-[#333333]">Dados de Acesso</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" value="Nome Completo *" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $student->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email" value="E-mail *" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $student->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
    <x-input-label for="password" value="Nova Senha Provisória" />
    <div x-data="{ show: false }" class="relative mt-1">
        <x-text-input x-bind:type="show ? 'text' : 'password'" type="password" id="password" name="password" class="block w-full pr-10" autocomplete="new-password" />
        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-codeforce-green">
            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" />
            </svg>
            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    </div>
    <p class="text-xs text-gray-500 mt-1">Preencha apenas se desejar redefinir a senha do aluno. A senha deve conter no mínio 8 caracteres, letras, números e caracteres especiais.</p>
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>
                        </div>
                    </section>

                    <!-- Seção 2: Pessoal -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-[#333333]">Dados Pessoais</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="cpf" value="CPF" />
                                <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full" :value="old('cpf', $student->cpf)" placeholder="000.000.000-00" oninput="maskCPF(this)" maxlength="14" />
                                <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="birth_date" value="Data de Nascimento" />
                                <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '')" />
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone" value="Telefone / Celular" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $student->phone)" placeholder="(00) 00000-0000" oninput="maskPhone(this)" maxlength="15" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>
                    </section>

                    <!-- Seção 3: Responsável -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-[#333333]">Dados do Responsável (Opcional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="guardian_name" value="Nome do Responsável" />
                                <x-text-input id="guardian_name" name="guardian_name" type="text" class="mt-1 block w-full" :value="old('guardian_name', $student->guardian_name)" />
                                <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="guardian_phone" value="Telefone do Responsável" />
                                <x-text-input id="guardian_phone" name="guardian_phone" type="text" class="mt-1 block w-full" :value="old('guardian_phone', $student->guardian_phone)" placeholder="(00) 00000-0000" oninput="maskPhone(this)" maxlength="15" />
                                <x-input-error :messages="$errors->get('guardian_phone')" class="mt-2" />
                            </div>
                        </div>
                    </section>

                    <!-- Seção 4: Endereço -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-[#333333]">Endereço</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="zip_code" value="CEP" />
                                <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code', $student->zip_code)" />
                                <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2 flex gap-4">
    <div class="flex-1">
        <x-input-label for="street" value="Endereço (Rua, Av.)" />
        <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" :value="old('street', $student->street)" />
        <x-input-error :messages="$errors->get('street')" class="mt-2" />
    </div>
    <div class="w-1/3">
        <x-input-label for="number" value="Número" />
        <x-text-input id="number" name="number" type="text" class="mt-1 block w-full" :value="old('number', $student->number ?? '')" />
        <x-input-error :messages="$errors->get('number')" class="mt-2" />
    </div>
</div>
                            <div>
                                <x-input-label for="neighborhood" value="Bairro" />
                                <x-text-input id="neighborhood" name="neighborhood" type="text" class="mt-1 block w-full" :value="old('neighborhood', $student->neighborhood)" />
                                <x-input-error :messages="$errors->get('neighborhood')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="city" value="Cidade" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $student->city)" />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="state" value="Estado (UF)" />
                                <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $student->state)" maxlength="2" />
                                <x-input-error :messages="$errors->get('state')" class="mt-2" />
                            </div>
                            <div class="md:col-span-3">
                                <x-input-label for="complement" value="Complemento" />
                                <x-text-input id="complement" name="complement" type="text" class="mt-1 block w-full" :value="old('complement', $student->complement)" />
                                <x-input-error :messages="$errors->get('complement')" class="mt-2" />
                            </div>
                        </div>
                    </section>

                    <!-- Seção 5: Documentos -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-[#333333]">Documentos (Anexos)</h3>
                        
                        @if($student->documents && count($student->documents) > 0)
                            <div class="mb-4">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Documentos já anexados:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($student->documents as $doc)
                                        <li class="text-sm">
                                            <a href="{{ $doc['url'] }}" target="_blank" class="text-codeforce-green hover:underline">
                                                {{ $doc['original_name'] ?? 'Documento Anexado' }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="attachments" value="Anexar novos arquivos (PDF, JPG, PNG)" />
                                <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-codeforce-green file:text-white hover:file:bg-[#008f7f] border border-gray-300 rounded-md transition cursor-pointer">
                                <p class="text-xs text-gray-500 mt-2">Você pode selecionar mais de um arquivo. Eles serão adicionados aos já existentes. O arquivo deve ter no máximo</p>
                                
                                @foreach($errors->get('attachments.*') as $errorArray)
                                    @foreach($errorArray as $errorMsg)
                                        <p class="text-sm text-red-600 mt-2">{{ $errorMsg }}</p>
                                    @endforeach
                                @endforeach
                                @error('attachments')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror

                            </div>
                        </div>
                    </section>

                    <div class="pt-6 flex justify-end gap-4 border-t border-gray-200">
                        <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-codeforce-green focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
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

    <script>
        function maskCPF(input) {
            let v = input.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            input.value = v;
        }

        function maskPhone(input) {
            let v = input.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
            v = v.replace(/(\d)(\d{4})$/, '$1-$2');
            input.value = v;
        }
    </script>
</x-app-layout>
