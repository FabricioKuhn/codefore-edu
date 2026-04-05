<x-app-layout>
    <x-slot name="header">
        <x-breadcrumbs :links="[
    ['name' => 'Home', 'url' => route(auth()->user()->role . '.dashboard')], 
    ['name' => 'Secretaria de Professores', 'url' => route(auth()->user()->role . '.teachers.index')],
    ['name' => 'Cadastrar Professor', 'url' => '#']
]" />
        <h2 class="text-xl font-semibold text-secondary leading-tight mt-2">
            Cadastrar Novo Professor
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <form action="{{ route(auth()->user()->role . '.teachers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-8 space-y-8">
                    
                    <!-- Seção 1: Acesso -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-secondary">Dados de Acesso</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" value="Nome Completo *" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email" value="E-mail *" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="password" value="Senha Provisória *" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="mb-6">
                                <x-input-label for="avatar" value="Foto do Professor" />
                                <input type="file" name="avatar" id="avatar" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                    </section>

                    <!-- Seção 2: Pessoal -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-secondary">Dados Pessoais</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="cpf" value="CPF" />
                                <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full" :value="old('cpf')" placeholder="000.000.000-00" />
                                <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="birth_date" value="Data de Nascimento" />
                                <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date')" />
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone" value="Telefone / Celular" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" placeholder="(00) 00000-0000" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>
                    </section>


                    <!-- Seção 4: Endereço -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-secondary">Endereço</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="zip_code" value="CEP" />
                                <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code')" />
                                <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="street" value="Endereço (Rua, Av.)" />
                                <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" :value="old('street')" />
                                <x-input-error :messages="$errors->get('street')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="neighborhood" value="Bairro" />
                                <x-text-input id="neighborhood" name="neighborhood" type="text" class="mt-1 block w-full" :value="old('neighborhood')" />
                                <x-input-error :messages="$errors->get('neighborhood')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="city" value="Cidade" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="state" value="Estado (UF)" />
                                <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state')" maxlength="2" />
                                <x-input-error :messages="$errors->get('state')" class="mt-2" />
                            </div>
                            <div class="md:col-span-3">
                                <x-input-label for="complement" value="Complemento" />
                                <x-text-input id="complement" name="complement" type="text" class="mt-1 block w-full" :value="old('complement')" />
                                <x-input-error :messages="$errors->get('complement')" class="mt-2" />
                            </div>
                        </div>
                    </section>

                    <!-- Seção 5: Documentos -->
                    <section>
                        <h3 class="font-semibold text-lg border-b border-gray-200 pb-2 mb-4 text-secondary">Documentos (Anexos)</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="attachments" value="Selecione múltiplos arquivos (PDF, JPG, PNG)" />
                                <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:brightness-90 transition-all duration-200 border border-gray-300 rounded-md transition cursor-pointer">
                                <p class="text-xs text-gray-500 mt-2">Você pode selecionar mais de um arquivo de uma vez (Max: 10MB por arquivo).</p>
                                <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
                            </div>
                        </div>
                    </section>

                    <div class="pt-6 flex justify-end gap-4 border-t border-gray-200">
                        <a href="{{ route(auth()->user()->role . '.teacher.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-25 transition">
                            Cancelar
                        </a>
                        <x-primary-button>
                            Criar Aluno
                        </x-primary-button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
