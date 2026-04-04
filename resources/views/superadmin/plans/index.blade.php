@extends('layouts.superadmin')

@section('header_title', 'Planos do Sistema')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-gray-500 text-sm font-medium">Gerencie os pacotes e limites de cada escola.</p>
        <a href="{{ route('superadmin.plans.create') }}" class="bg-primary text-white px-5 py-2 rounded-md font-bold text-[11px] uppercase tracking-widest hover:bg-opacity-90 transition">
            Novo Plano
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            {{-- Procure a <thead> e ajuste --}}


{{-- Procure o @foreach e ajuste as <td> --}}
{{-- Procure a <thead> e ajuste --}}
<thead class="bg-gray-50 border-b border-gray-100">
    <tr>
        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Plano</th>
        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Clientes Ativos</th>
        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Preços (M/A)</th>
        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Limites (T / A / M)</th>
        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Status</th>
        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Ações</th>
    </tr>
</thead>

{{-- Procure o @foreach e ajuste as <td> --}}
<tbody class="divide-y divide-gray-50">
    @foreach($plans as $plan)
    <tr class="hover:bg-gray-50/50 transition">
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-secondary text-sm">{{ $plan->name }}</span>
                @if($plan->is_free) 
                    <span class="bg-blue-50 text-blue-500 text-[9px] font-black px-2 py-0.5 rounded border border-blue-100 uppercase tracking-tighter">Base Grátis</span> 
                @endif
            </div>
        </td>

        {{-- COLUNA: CLIENTES ATIVOS --}}
        <td class="px-6 py-4 text-center">
            <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-bold leading-none text-primary bg-green-50 rounded-full border border-green-100">
                {{ $plan->institutions_count }}
            </span>
        </td>

        <td class="px-6 py-4 text-xs font-bold text-gray-500">
            R$ {{ number_format($plan->price_monthly, 2, ',', '.') }} 
            <span class="text-gray-200 mx-1">|</span> 
            <span class="text-gray-400">R$ {{ number_format($plan->price_yearly, 2, ',', '.') }}</span>
        </td>

        <td class="px-6 py-4">
            <div class="flex gap-1">
                <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded font-bold" title="Turmas">{{ $plan->limit_classes ?? '∞' }}</span>
                <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded font-bold" title="Alunos/Turma">{{ $plan->limit_students_per_class ?? '∞' }}</span>
                <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded font-bold" title="Missões">{{ $plan->limit_tasks_per_class ?? '∞' }}</span>
            </div>
        </td>

        {{-- COLUNA: STATUS --}}
        <td class="px-6 py-4 text-center">
            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $plan->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
            </span>
        </td>

        <td class="px-6 py-4 text-right space-x-2">
            <a href="{{ route('superadmin.plans.edit', $plan) }}" class="text-gray-400 hover:text-primary transition inline-block">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            
            <form action="{{ route('superadmin.plans.toggle', $plan) }}" method="POST" class="inline-block">
                @csrf @method('PATCH')
                <button type="submit" class="text-gray-400 hover:{{ $plan->is_active ? 'text-red-500' : 'text-primary' }} transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</tbody>
        </table>
    </div>
</div>
@endsection