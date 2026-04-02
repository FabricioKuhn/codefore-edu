<x-app-layout>
    <x-slot name="header">  
        <x-breadcrumbs :links="[
    ['name' => 'Home', 'url' => route('dashboard')],
    ['name' => 'Minhas Turmas', 'url' => route('classrooms.index')],
    ['name' => $classroom->name, 'url' => route('classrooms.show', $classroom)]
]" />
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-[#333333] leading-tight">
                {{ $classroom->name }} - {{ $classroom->subject }}
            </h2>
            <a href="{{ route('classrooms.activities.create', $classroom) }}">
                <x-primary-button type="button">Nova Missão</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-codeforce-green text-codeforce-green px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-6 bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-[#333333]">{{ $classroom->name }}</h3>
                    <p class="text-gray-500">{{ $classroom->subject }}</p>
                </div>
                <div class="mt-4 md:mt-0 text-center bg-gray-100 p-4 rounded-lg">
                    <span class="text-sm uppercase text-gray-500 font-semibold tracking-wider">Código de Convite</span>
                    <div class="text-3xl font-mono font-bold tracking-widest mt-1 text-codeforce-green">{{ $classroom->join_code }}</div>
                </div>
            </div>

            <h3 class="text-xl font-bold text-[#333333] mb-4 px-2">Missões / Avaliações</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($classroom->activities as $activity)
                    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 border-t-4 border-t-codeforce-green p-6">
                        <h4 class="text-lg font-bold mb-2 text-[#333333]">{{ $activity->title }}</h4>
                        
                        <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                            <span class="font-medium text-[#00ad9a]">XP Base: {{ $activity->base_xp }}</span>
                            <span class="px-2 py-1 bg-gray-100 rounded-md">Status: {{ ucfirst($activity->status) }}</span>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                            <a href="{{ route('activities.show', $activity) }}" class="text-codeforce-green hover:text-[#008f7f] font-semibold text-sm">Gerenciar Missão &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500 border border-gray-100">
                        Nenhuma missão cadastrada nesta turma ainda. Comece criando a primeira!
                    </div>
                @endforelse
            </div>
            <div class="mt-12 flex items-center justify-between mb-4 px-2" x-data>
                <h3 class="text-xl font-bold text-[#333333]">Alunos Matriculados</h3>
                <x-primary-button type="button" @click="$dispatch('open-student-modal')">Cadastrar Aluno</x-primary-button>
            </div>
            
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                @if($classroom->students->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="border-b border-gray-200 py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Nome</th>
                                    <th class="border-b border-gray-200 py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Email</th>
                                    <th class="border-b border-gray-200 py-3 px-4 text-sm font-semibold text-gray-600 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classroom->students as $student)
                                    <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-b-0">
                                        <td class="py-3 px-4 text-[#333333] font-medium">{{ $student->name }}</td>
                                        <td class="py-3 px-4 text-gray-500">{{ $student->email }}</td>
                                        <td class="py-3 px-4">
                                            <a href="#" class="text-gray-400 hover:text-red-500 text-sm font-semibold transition" onclick="alert('Funcionalidade de remover no futuro')">Remover</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-gray-500 py-4">
                        Nenhum aluno matriculado nesta turma ainda.
                    </div>
                @endif
            </div>

        </div> <!-- End of py-12 / max-w-7xl -->

        <!-- Modal de Matrícula -->
        <div x-data="{ openStudentModal: {{ $errors->has('email') ? 'true' : 'false' }} }" @open-student-modal.window="openStudentModal = true">
            <div x-show="openStudentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="openStudentModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="openStudentModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div x-show="openStudentModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-md w-full">
                         
                        <form action="{{ route('classrooms.students.store', $classroom) }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-[#333333] mb-4" id="modal-title">Cadastrar Novo Aluno</h3>
                                
                                <div class="mb-4">
                                    <x-input-label for="name" value="Nome Completo" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="email" value="Email" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="password" value="Senha Provisória" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                                <x-primary-button class="w-full sm:ml-3 sm:w-auto">Matricular</x-primary-button>
                                <button type="button" @click="openStudentModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
