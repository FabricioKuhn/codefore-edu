<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($tenant->trading_name ?? 'CodeForce') }} | {{ $title ?? config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        {{-- Favicon Dinâmico com Fallback --}}
@php
    // 1. Tenta pegar o ícone da instituição (tenant)
    // 2. Se não existir instituição ou não tiver ícone, usa o padrão da pasta public
    $faviconUrl = (isset($tenant) && $tenant->flat_icon) 
        ? asset('storage/' . $tenant->flat_icon) 
        : asset('favicon-codeforce.png'); 
@endphp

<link rel="icon" type="image/png" href="{{ $faviconUrl }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css'])
        <style>
    :root {
        /* Se houver tenant, usa a cor dele. Se não, usa o verde CodeForce */
        --primary-color: {{ $tenant->primary_color ?? '#00ad9a' }};
        --secondary-color: {{ $tenant->secondary_color ?? '#333333' }};
        --tertiary-color: {{ $tenant->tertiary_color ?? '#ffffff' }};
    }
</style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            
                <main class="max-w-7xl mx-auto py-6">
    {{-- Bloco de Mensagens de Erro/Sucesso --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
    @if (session('success'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 5000)" 
             x-show="show" 
             x-transition.duration.500ms
             class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl shadow-sm flex justify-between items-center">
            
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-bold text-sm tracking-tight">{{ session('success') }}</span>
            </div>

            <button @click="show = false" class="text-green-400 hover:text-green-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" x-transition.duration.500ms
             class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl shadow-sm flex justify-between items-center">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="font-bold text-sm tracking-tight">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-red-400 hover:text-red-600">&times;</button>
        </div>
    @endif
</div>

    {{ $slot }}
</main>
                
        </div>

        <!-- Scripts -->
        @vite(['resources/js/app.js'])
    </body>
</html>
