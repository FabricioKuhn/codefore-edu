@extends('layouts.superadmin')

@section('header_title', 'Novo Plano de Assinatura')

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('superadmin.plans.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 md:col-span-7 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-black text-primary uppercase tracking-widest mb-4">Identificação</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase">Nome do Plano</label>
                        <input type="text" name="name" required placeholder="Ex: CodeForce Start" 
                               class="w-full border-gray-200 rounded-lg text-sm mt-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                        <input type="checkbox" name="is_free" id="is_free" value="1" class="rounded text-primary focus:ring-primary">
                        <label for="is_free" class="text-xs font-bold text-secondary uppercase cursor-pointer">Definir como Plano Gratuito (Base de Cadastro)</label>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-5 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-black text-primary uppercase tracking-widest mb-4">Precificação</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase">Valor Mensal (R$)</label>
                        <input type="number" step="0.01" name="price_monthly" value="0.00" 
                               class="w-full border-gray-200 rounded-lg text-sm mt-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase">Valor Anual (R$)</label>
                        <input type="number" step="0.01" name="price_yearly" value="0.00" 
                               class="w-full border-gray-200 rounded-lg text-sm mt-1 focus:ring-primary">
                    </div>
                </div>
            </div>

            <div class="col-span-12 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-black text-primary uppercase tracking-widest mb-6">Limites de Utilização</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    @php 
                        $limits = [
                            'limit_classes' => 'Qtd. de Turmas',
                            'limit_students_per_class' => 'Alunos por Turma',
                            'limit_tasks_per_class' => 'Atividades por Turma'
                        ];
                    @endphp

                    @foreach($limits as $field => $label)
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-secondary uppercase">{{ $label }}</label>
                        <div class="relative flex items-center">
                            <input type="number" name="{{ $field }}" id="{{ $field }}" 
                                   class="w-full border-gray-200 rounded-lg text-sm pr-20 focus:ring-primary focus:border-primary transition-all">
                            <button type="button" onclick="toggleUnlimited('{{ $field }}')" 
                                    class="absolute right-1 px-2 py-1 bg-gray-100 text-[9px] font-black text-gray-400 rounded-md hover:bg-secondary hover:text-white transition uppercase">
                                Ilimitado
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('superadmin.plans.index') }}" class="px-6 py-2 text-gray-400 text-xs font-black uppercase hover:text-secondary transition">Cancelar</a>
            <button type="submit" class="bg-primary text-white px-10 py-2 rounded-lg font-black text-xs uppercase tracking-widest hover:bg-opacity-90 shadow-md transition">
                Salvar Plano
            </button>
        </div>
    </form>
</div>

<script>
function toggleUnlimited(fieldId) {
    const input = document.getElementById(fieldId);
    if (input.placeholder === "ILIMITADO") {
        input.placeholder = "";
        input.value = "";
        input.readOnly = false;
        input.classList.remove('bg-gray-50', 'text-primary', 'font-black');
    } else {
        input.value = "";
        input.placeholder = "ILIMITADO";
        input.readOnly = true;
        input.classList.add('bg-gray-50', 'text-primary', 'font-black');
    }
}
</script>
@endsection