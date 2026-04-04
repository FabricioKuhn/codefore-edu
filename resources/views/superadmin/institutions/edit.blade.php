@extends('layouts.superadmin')

@section('content')
<div class="p-8 max-w-6xl mx-auto">
    {{-- Breadcrumb --}}
    <nav class="flex text-gray-500 text-sm mb-4">
        <ol class="inline-flex items-center space-x-1">
            <li><a href="{{ route('superadmin.institutions.index') }}" class="hover:text-primary">Instituições</a></li>
            <li><span class="mx-2 text-gray-400">/</span></li>
            <li class="text-secondary font-bold">Editar Instituição</li>
        </ol>
    </nav>

    <div class="flex justify-between items-center mb-6">
        <h2 class="font-bold text-2xl text-secondary">Editar: {{ $institution->trading_name }}</h2>
    </div>

    <form action="{{ route('superadmin.institutions.update', $institution) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- CARD 1: DADOS FISCAIS --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-primary uppercase mb-4 border-b pb-2">Dados Fiscais</h3>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-8">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Nome Fantasia</label>
                    <input type="text" name="trading_name" value="{{ $institution->trading_name }}" class="w-full border-gray-300 rounded-md focus:border-primary focus:ring-0 text-sm mt-1">
                </div>
                {{-- NOVO CAMPO DE DOMÍNIO --}}
    <div class="col-span-12 md:col-span-4">
        <label class="text-[11px] font-bold text-primary uppercase">Domínio de Acesso (URL)</label>
        <input type="text" name="domain" value="{{ $institution->domain }}" placeholder="ex: cliente.local" 
               class="w-full border-primary rounded-md focus:ring-0 text-sm mt-1 bg-green-50/30">
    </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">CNPJ*</label>
                    <input type="text" name="cnpj" value="{{ $institution->cnpj }}" required class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
                <div class="col-span-12">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Razão Social*</label>
                    <input type="text" name="company_name" value="{{ $institution->company_name }}" required class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Indicador IE</label>
                    <select name="ie_indicator" class="w-full border-gray-300 rounded-md text-sm mt-1">
                        <option value="contribuinte" {{ $institution->ie_indicator == 'contribuinte' ? 'selected' : '' }}>Contribuinte</option>
                        <option value="nao_contribuinte" {{ $institution->ie_indicator == 'nao_contribuinte' ? 'selected' : '' }}>Não Contribuinte</option>
                        <option value="isento" {{ $institution->ie_indicator == 'isento' ? 'selected' : '' }}>Isento</option>
                    </select>
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Inscrição Estadual</label>
                    <input type="text" name="state_registration" value="{{ $institution->state_registration }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Inscrição Municipal</label>
                    <input type="text" name="municipal_registration" value="{{ $institution->municipal_registration }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
            </div>
        </div>

        {{-- CARD 2: ENDEREÇO E CONTATO --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-primary uppercase mb-4 border-b pb-2">Contato e Endereço</h3>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-7">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">E-mail</label>
                    <input type="email" name="email" value="{{ $institution->email }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
                <div class="col-span-12 md:col-span-5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Telefone</label>
                    <input type="text" name="phone" value="{{ $institution->phone }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>

                {{-- Grid Compacto para Endereço --}}
                <div class="col-span-12 md:col-span-3">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">CEP</label>
                    <input type="text" name="zip_code" value="{{ $institution->zip_code }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
                <div class="col-span-12 md:col-span-7">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Endereço</label>
                    <input type="text" name="street" value="{{ $institution->street }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
                <div class="col-span-12 md:col-span-2">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Nº</label>
                    <input type="text" name="number" value="{{ $institution->number }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>

                <div class="col-span-12 md:col-span-5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Bairro</label>
                    <input type="text" name="neighborhood" value="{{ $institution->neighborhood }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
                <div class="col-span-12 md:col-span-5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Cidade</label>
                    <input type="text" name="city" value="{{ $institution->city }}" class="w-full border-gray-300 rounded-md text-sm mt-1">
                </div>
                <div class="col-span-12 md:col-span-2">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-tight">Estado (UF)</label>
                    <input type="text" name="state" value="{{ $institution->state }}" maxlength="2" class="w-full border-gray-300 rounded-md text-sm mt-1 uppercase text-center">
                </div>
            </div>
        </div>

        {{-- CARD 3: WHITE LABEL --}}
        <div class="bg-secondary p-6 rounded-lg shadow-sm text-white">
            <h3 class="text-sm font-bold text-primary uppercase mb-4 border-b border-gray-700 pb-2">Identidade Visual White Label</h3>
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Cor Primária</label>
                    <input type="color" name="primary_color" value="{{ $institution->primary_color ?? '#00ad9a' }}" class="w-full h-10 rounded cursor-pointer mt-1 border-0">
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Cor Secundária</label>
                    <input type="color" name="secondary_color" value="{{ $institution->secondary_color ?? '#333333' }}" class="w-full h-10 rounded cursor-pointer mt-1 border-0">
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Cor Terciária</label>
                    <input type="color" name="tertiary_color" value="{{ $institution->tertiary_color ?? '#ffffff' }}" class="w-full h-10 rounded cursor-pointer mt-1 border-0">
                </div>

                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Logo Original</label>
                    <input type="file" name="logo_original" class="block w-full text-xs text-gray-300 mt-1">
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Logo Negativa</label>
                    <input type="file" name="logo_negative" class="block w-full text-xs text-gray-300 mt-1">
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Flat Icon</label>
                    <input type="file" name="flat_icon" class="block w-full text-xs text-gray-300 mt-1">
                </div>

                <div class="col-span-12 md:col-span-6">
    <label class="text-[10px] font-black text-gray-400 uppercase">Plano da Instituição</label>
    <select name="plan_id" class="w-full border-gray-200 rounded-lg text-sm mt-1 focus:ring-primary focus:border-primary">
        <option value="">Selecione um Plano</option>
        @foreach($plans as $plan)
            <option value="{{ $plan->id }}" {{ (isset($institution) && $institution->plan_id == $plan->id) ? 'selected' : '' }}>
                {{ $plan->name }} (R$ {{ number_format($plan->price_monthly, 2, ',', '.') }})
            </option>
        @endforeach
    </select>
</div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('superadmin.institutions.index') }}" class="px-6 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-md transition">Cancelar</a>
            <x-primary-button>
                Atualizar Instituição
            </x-primary-button>
        </div>
    </form>
</div>
@endsection