<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistema') }}</title>

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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    @if(session('error'))
        <div class="mb-4 mx-4 sm:mx-6 lg:px-8">
            <div class="bg-red-500 text-white px-4 py-3 rounded-lg shadow-md font-bold">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 mx-4 sm:mx-6 lg:px-8">
            <div class="bg-emerald-500 text-white px-4 py-3 rounded-lg shadow-md font-bold">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{ $slot }}
</main>
                
        </div>
    </body>
</html>
