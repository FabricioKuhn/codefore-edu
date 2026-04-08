<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    
                    {{-- MENU DO ADMIN DA INSTITUIÇÃO --}}
                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.classrooms.index')" :active="request()->routeIs('admin.classrooms.*')">
                            {{ __('Turmas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')">
                            {{ __('Alunos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.teachers.index')" :active="request()->routeIs('admin.teachers.*')">
                            {{ __('Professores') }}
                        </x-nav-link>
                    @endif

                    {{-- MENU DO PROFESSOR --}}
                    @if(auth()->user()->role === 'teacher')
                        <x-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.classrooms.index')" :active="request()->routeIs('teacher.classrooms.*')">
                            {{ __('Minhas Turmas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.students.index')" :active="request()->routeIs('teacher.students.*')">
                            {{ __('Meus Alunos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.questions.index')" :active="request()->routeIs('teacher.questions.*')">
                            {{ __('Banco de Questões') }}
                        </x-nav-link>   
                    @endif

                    {{-- MENU DO ALUNO --}}
                    @if(auth()->user()->role === 'student')
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                            {{ __('Meu Perfil') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.classrooms.index')" :active="request()->routeIs('student.classrooms.*')">
                            {{ __('Minha Turma') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.feed')" :active="request()->routeIs('student.feed')">
                            {{ __('Feed') }}
                        </x-nav-link>
                    @endif

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover border border-gray-200 shadow-sm">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=00ad9a&background=E5F7F5&rounded=true" alt="Avatar" class="h-8 w-8 rounded-full border border-gray-200 shadow-sm">
                                @endif
                                <span class="font-medium">{{ Auth::user()->name }}</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Meu Perfil') }}
                        </x-dropdown-link>

                        {{-- Assinatura só faz sentido se for Admin da Instituição --}}
                        @if(auth()->user()->role === 'admin')
                            <x-dropdown-link href="#">
                                {{ __('Assinatura') }}
                            </x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            
            {{-- MENU MOBILE DO ADMIN DA INSTITUIÇÃO --}}
            @if(auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.classrooms.index')" :active="request()->routeIs('admin.classrooms.*')">
                    {{ __('Turmas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')">
                    {{ __('Alunos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.teachers.index')" :active="request()->routeIs('admin.teachers.*')">
                    {{ __('Professores') }}
                </x-responsive-nav-link>
            @endif

            {{-- MENU MOBILE DO PROFESSOR --}}
            @if(auth()->user()->role === 'teacher')
                <x-responsive-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.classrooms.index')" :active="request()->routeIs('teacher.classrooms.*')">
                    {{ __('Minhas Turmas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.students.index')" :active="request()->routeIs('teacher.students.*')">
                    {{ __('Meus Alunos') }}
                </x-responsive-nav-link>
            @endif

            {{-- MENU MOBILE DO ALUNO --}}
            @if(auth()->user()->role === 'student')
                <x-responsive-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                    {{ __('Meu Perfil') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.classrooms.index')" :active="request()->routeIs('student.classrooms.*')">
                    {{ __('Minha Turma') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.feed')" :active="request()->routeIs('student.feed')">
                    {{ __('Feed') }}
                </x-responsive-nav-link>
            @endif

        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Meu Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>