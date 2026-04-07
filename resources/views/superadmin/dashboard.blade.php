@extends('layouts.superadmin')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-gray-500 text-sm font-medium">Clientes Cadastrados</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-gray-500 text-sm font-medium">Alunos na Plataforma</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-gray-500 text-sm font-medium">XP Gerada (30 dias)</h3>
            <p class="text-3xl font-bold text-emerald-500 mt-2">0</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-gray-500 text-sm font-medium">Novos Clientes (30 dias)</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
        </div>

    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ações Rápidas</h3>
        <div class="flex space-x-4">
            <a href="{{ route('superadmin.institutions.create') }}" class="px-4 py-2 bg-emerald-500 text-white rounded-lg  font-medium transition-colors">
                + Novo Cliente
            </a>
            <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">
                + Novo Contrato / Assinatura
            </button>
        </div>
    </div>
@endsection