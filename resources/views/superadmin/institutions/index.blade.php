@extends('layouts.superadmin')

@section('content')
<div class="p-8">
    {{-- 1. BREADCRUMB --}}
    <nav class="flex text-gray-500 text-sm mb-4">
        <ol class="inline-flex items-center space-x-1">
            <li><a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary">Home</a></li>
            <li><span class="mx-2 text-gray-400">/</span></li>
            <li class="text-secondary font-bold">Instituições</li>
        </ol>
    </nav>

    {{-- 2. HEADER --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="font-semibold text-xl text-secondary leading-tight">Gestão de Instituições</h2>
        <a href="{{ route('superadmin.institutions.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest  shadow-md transition">
            + Novo Cadastro
        </a>
    </div>

    {{-- 3. FILTRO --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('superadmin.institutions.index') }}" class="w-full md:w-1/3 flex">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou CNPJ..." 
                class="w-full rounded-l-md border-gray-300 border-r-0 focus:ring-0 focus:border-primary" />
            <button type="submit" class="px-4 py-2 bg-gray-200 border border-gray-300 rounded-r-md text-secondary hover:bg-gray-300 font-semibold transition">
                Filtrar
            </button>
        </form>
    </div>

    {{-- 4. TABELA --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-secondary uppercase bg-gray-50 border-b">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Nome</th>
                        <th scope="col" class="px-6 py-3">CNPJ</th>
                        <th scope="col" class="px-6 py-3">Telefone</th>
                        <th scope="col" class="px-6 py-3">E-mail</th>
                        <th scope="col" class="px-6 py-3">Data de cadastro</th>
                        <th scope="col" class="px-6 py-3 text-center">Alunos ativos</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($institutions as $inst)
                        <tr class="bg-white border-b hover:bg-gray-50 transition">
                            {{-- ID --}}
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                #{{ str_pad($inst->id, 4, '0', STR_PAD_LEFT) }}
                            </td>

                            {{-- NOME --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-secondary">{{ $inst->trading_name ?? '—' }}</div>
                                <div class="text-[10px] text-gray-400 uppercase">{{ $inst->company_name ?? '—' }}</div>
                            </td>

                            {{-- CNPJ --}}
                            <td class="px-6 py-4 font-medium text-gray-600">
                                {{ $inst->cnpj ?? '—' }}
                            </td>

                            {{-- TELEFONE --}}
                            <td class="px-6 py-4">
                                {{ $inst->phone ?? '—' }}
                            </td>

                            {{-- E-MAIL --}}
                            <td class="px-6 py-4">
                                {{ $inst->email ?? '—' }}
                            </td>

                            {{-- DATA --}}
                            <td class="px-6 py-4">
                                {{ $inst->created_at ? $inst->created_at->format('d/m/Y') : '—' }}
                            </td>

                            {{-- ALUNOS --}}
                            <td class="px-6 py-4 text-center font-bold text-secondary">
                                {{ $inst->active_students ?? 0 }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-6 py-4">
                                @if($inst->status)
                                    <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded">ATIVO</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded">BLOQUEADO</span>
                                @endif
                            </td>

                            {{-- AÇÕES --}}
                            <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap flex justify-end">
                                <a href="{{ route('superadmin.institutions.edit', $inst) }}" class="inline-block transition" data-tooltip="Editar Instituição">
                                    <svg class="w-5 h-5 text-amber-500 hover:text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>

                                <form action="{{ route('superadmin.institutions.toggle-status', $inst) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-block transition" data-tooltip="{{ $inst->status ? 'Bloquear Instituição' : 'Desbloquear Instituição' }}"
                                        onclick="return confirm('Deseja realmente {{ $inst->status ? 'bloquear' : 'desbloquear' }} esta instituição?')">
                                        @if($inst->status)
                                            <svg class="w-5 h-5 text-red-600 hover:text-red-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.524a6 6 0 018.367 8.366L13.477 14.89M9.224 5.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <svg class="w-5 h-5 text-emerald-600 hover:text-emerald-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                Nenhuma instituição encontrada no banco de dados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($institutions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection