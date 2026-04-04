@extends('layouts.superadmin')

@section('content')
<div class="p-8 max-w-6xl mx-auto">
    <nav class="flex text-gray-500 text-sm mb-4">
        <ol class="inline-flex items-center space-x-1">
            <li><a href="{{ route('superadmin.institutions.index') }}" class="hover:text-[#00ad9a]">Instituições</a></li>
            <li><span class="mx-2 text-gray-400">/</span></li>
            <li class="text-[#333333] font-bold">{{ isset($institution) ? 'Editar' : 'Novo Cadastro' }}</li>
        </ol>
    </nav>

    <form action="{{ isset($institution) ? route('superadmin.institutions.update', $institution) : route('superadmin.institutions.store') }}" 
          method="POST" enctype="multipart/form-data" class="space-y-6 mt-6">
        @csrf
        @if(isset($institution)) @method('PUT') @endif

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-[#00ad9a] uppercase mb-4 border-b pb-2">Dados Fiscais</h3>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-9">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Nome Fantasia</label>
                    <input type="text" name="trading_name" value="{{ $institution->trading_name ?? old('trading_name') }}" class="w-full border-gray-300 rounded-md focus:border-[#00ad9a] focus:ring-0 text-sm">
                </div>
                {{-- NOVO CAMPO DE DOMÍNIO --}}
    <div class="col-span-12 md:col-span-4">
        <label class="text-[11px] font-bold text-[#00ad9a] uppercase">Domínio de Acesso (URL)</label>
        <input type="text" name="domain" placeholder="ex: cliente.local ou sistema.cliente.com.br" 
               class="w-full border-[#00ad9a] rounded-md focus:ring-0 text-sm mt-1 bg-green-50/30">
    </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">CNPJ*</label>
                    <input type="text" name="cnpj" value="{{ $institution->cnpj ?? old('cnpj') }}" required class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="col-span-12">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Razão Social*</label>
                    <input type="text" name="company_name" value="{{ $institution->company_name ?? old('company_name') }}" required class="w-full border-gray-300 rounded-md text-sm">
                </div>
                
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Indicador IE</label>
                    <select name="ie_indicator" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="contribuinte" {{ (isset($institution) && $institution->ie_indicator == 'contribuinte') ? 'selected' : '' }}>Contribuinte</option>
                        <option value="nao_contribuinte" {{ (isset($institution) && $institution->ie_indicator == 'nao_contribuinte') ? 'selected' : '' }}>Não Contribuinte</option>
                        <option value="isento" {{ (isset($institution) && $institution->ie_indicator == 'isento') ? 'selected' : '' }}>Isento</option>
                    </select>
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Inscrição Estadual</label>
                    <input type="text" name="state_registration" value="{{ $institution->state_registration ?? old('state_registration') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Inscrição Municipal</label>
                    <input type="text" name="municipal_registration" value="{{ $institution->municipal_registration ?? old('municipal_registration') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-[#00ad9a] uppercase mb-4 border-b pb-2">Contato e Endereço</h3>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-7">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">E-mail</label>
                    <input type="email" name="email" value="{{ $institution->email ?? old('email') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="col-span-12 md:col-span-5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Telefone</label>
                    <input type="text" name="phone" value="{{ $institution->phone ?? old('phone') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                
                <div class="col-span-12 md:col-span-3">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">CEP</label>
                    <input type="text" name="zip_code" value="{{ $institution->zip_code ?? old('zip_code') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="col-span-12 md:col-span-7">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Endereço</label>
                    <input type="text" name="street" value="{{ $institution->street ?? old('street') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="col-span-12 md:col-span-2">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Nº</label>
                    <input type="text" name="number" value="{{ $institution->number ?? old('number') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>

                <div class="col-span-12 md:col-span-5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Bairro</label>
                    <input type="text" name="neighborhood" value="{{ $institution->neighborhood ?? old('neighborhood') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="col-span-12 md:col-span-5">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Cidade</label>
                    <input type="text" name="city" value="{{ $institution->city ?? old('city') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div class="col-span-12 md:col-span-2">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">UF</label>
                    <input type="text" name="state" value="{{ $institution->state ?? old('state') }}" maxlength="2" class="w-full border-gray-300 rounded-md text-sm uppercase text-center">
                </div>
            </div>
        </div>

        <div class="bg-[#333333] p-6 rounded-lg shadow-md text-white">
            <h3 class="text-sm font-bold text-[#00ad9a] uppercase mb-4 border-b border-gray-700 pb-2">Identidade Visual White Label</h3>
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
                
                <div class="col-span-12 md:col-span-4">
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Plano</label>
                    <select name="plan" class="w-full border-gray-600 rounded-md text-sm bg-gray-800 text-white">
                        <option value="">A definir</option>
                        <option value="start" {{ (isset($institution) && $institution->plan == 'start') ? 'selected' : '' }}>Start</option>
                        <option value="pro" {{ (isset($institution) && $institution->plan == 'pro') ? 'selected' : '' }}>Pro</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('superadmin.institutions.index') }}" class="px-6 py-2 text-gray-500 font-bold hover:text-[#333333] transition">Cancelar</a>
            <button type="submit" class="px-10 py-2 bg-[#00ad9a] text-white font-bold rounded-md hover:bg-[#009688] shadow-lg transition">
                {{ isset($institution) ? 'Atualizar Instituição' : 'Salvar Instituição' }}
            </button>
        </div>
    </form>
</div>
@endsection