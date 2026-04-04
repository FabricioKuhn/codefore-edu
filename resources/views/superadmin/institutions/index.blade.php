@extends('layouts.superadmin')

@section('content')
<div class="p-8">
    {{-- 1. BREADCRUMB --}}
    <nav class="flex text-gray-500 text-sm mb-4">
        <ol class="inline-flex items-center space-x-1">
            <li><a href="{{ route('superadmin.dashboard') }}" class="hover:text-[#00ad9a]">Home</a></li>
            <li><span class="mx-2 text-gray-400">/</span></li>
            <li class="text-[#333333] font-bold">Instituições</li>
        </ol>
    </nav>

    {{-- 2. HEADER --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="font-semibold text-xl text-[#333333] leading-tight">Gestão de Instituições</h2>
        <a href="{{ route('superadmin.institutions.create') }}" class="inline-flex items-center px-4 py-2 bg-[#00ad9a] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#009688] transition shadow-md">
            + Novo Cadastro
        </a>
    </div>

    {{-- 3. FILTRO --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('superadmin.institutions.index') }}" class="w-full md:w-1/3 flex">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou CNPJ..." 
                class="w-full rounded-l-md border-gray-300 border-r-0 focus:ring-0 focus:border-[#00ad9a]" />
            <button type="submit" class="px-4 py-2 bg-gray-200 border border-gray-300 rounded-r-md text-[#333333] hover:bg-gray-300 font-semibold transition">
                Filtrar
            </button>
        </form>
    </div>

    {{-- 4. TABELA --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-[#333333] uppercase bg-gray-50 border-b">
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
                                <div class="font-bold text-[#333333]">{{ $inst->trading_name ?? '—' }}</div>
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
                            <td class="px-6 py-4 text-center font-bold text-[#333333]">
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
                            <td class="px-6 py-4 text-right flex justify-end gap-3">
                                <a href="{{ route('superadmin.institutions.edit', $inst) }}" class="text-[#00ad9a] font-bold hover:underline">
        Editar
    </a>

    <form action="{{ route('superadmin.institutions.toggle-status', $inst) }}" method="POST" class="inline">
        @csrf
        @method('PATCH')
        <button type="submit" 
            onclick="return confirm('Deseja realmente {{ $inst->status ? 'bloquear' : 'desbloquear' }} esta instituição?')"
            class="font-bold transition {{ $inst->status ? 'text-gray-400 hover:text-red-600' : 'text-green-500 hover:text-green-700' }}">
            {{ $inst->status ? 'Bloquear' : 'Desbloquear' }}
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