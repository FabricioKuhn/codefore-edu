<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin') - CodeForce</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased flex h-screen overflow-hidden">

    <aside class="w-64 bg-gray-900 text-white flex flex-col h-full">
        <div class="h-16 flex items-center justify-center border-b border-gray-800">
            <a href="">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.dashboard') ? 'bg-gray-800 border-l-4 border-emerald-500' : '' }}">
                <span class="font-medium">Dashboard</span>
            </a>
            
            <a href="{{ route('superadmin.institutions.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('superadmin.institutions.*') ? 'bg-gray-800 border-l-4 border-emerald-500' : '' }}">
                <span class="font-medium">Instituições (Escolas)</span>
            </a>

            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors text-gray-400">
                <span class="font-medium">Planos</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors text-gray-400">
                <span class="font-medium">Assinaturas</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-red-400 hover:bg-gray-800 rounded-lg transition-colors">
                    Sair do Sistema
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10">
            <h2 class="text-xl font-semibold text-gray-800">
                @yield('header_title', 'Painel Administrativo')
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500">Olá, {{ auth()->user()->name }}</span>
                <div class="h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded-r-md">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

</body>
</html>