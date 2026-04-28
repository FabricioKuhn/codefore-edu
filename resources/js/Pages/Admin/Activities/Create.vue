<template>
    <AppLayout>
        <Head :title="`Nova Avaliação - ${classroom.name}`" />

        <div class="py-12">
            <div class="max-w-[1600px] mx-auto px-6 mb-6">
                 <nav class="flex text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-2">
                    <ol class="inline-flex items-center space-x-2">
                        <li><Link :href="route(user.role + '.dashboard')" class="hover:text-primary transition-colors">Home</Link></li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right text-[8px] mx-2"></i>
                            <Link :href="route(user.role + '.classrooms.index')" class="hover:text-primary transition-colors">Turmas</Link>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right text-[8px] mx-2"></i>
                            <span class="text-gray-500">Nova Avaliação</span>
                        </li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-secondary">Criar {{ form.type === 'exam' ? 'Prova Dinâmica' : 'Tarefa' }}</h2>
            </div>

            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-8">
                    <form @submit.prevent="submit">
                        <div class="mb-8 p-6 bg-blue-50/50 border border-blue-100 rounded-2xl">
                            <label class="block text-blue-800 font-black uppercase tracking-widest text-[10px] mb-3">Formato da Avaliação</label>
                            <select v-model="form.type" class="block w-full md:w-2/3 border-blue-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm font-bold text-secondary cursor-pointer">
                                <option value="task">Tarefa (Questões fixas)</option>
                                <option value="exam">Prova Dinâmica (Sorteio aleatório)</option>
                            </select>
                        </div>

                        <div v-if="form.type === 'exam'" class="mb-8 p-6 bg-purple-50 border border-purple-100 rounded-2xl">
                            <h3 class="text-purple-800 font-black uppercase tracking-widest text-[10px] mb-2">Configuração do Sorteio</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Qtd. Múltipla Escolha</label>
                                    <input type="number" v-model="form.exam_settings.multiple_choice" class="w-full border-gray-200 rounded-xl h-11 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Qtd. Descritivas</label>
                                    <input type="number" v-model="form.exam_settings.descriptive" class="w-full border-gray-200 rounded-xl h-11 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 mb-8">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Título da Avaliação</label>
                                <input v-model="form.title" type="text" class="w-full border-gray-200 rounded-xl h-11 text-sm" required>
                                <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Instruções</label>
                                <textarea v-model="form.description" rows="3" class="w-full border-gray-200 rounded-xl text-sm"></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 border-t border-gray-100 pt-8 mb-8">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Início</label>
                                <input v-model="form.start_date" type="datetime-local" class="w-full border-gray-200 rounded-xl h-11 text-[11px]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Prazo Final</label>
                                <input v-model="form.end_date" type="datetime-local" class="w-full border-gray-200 rounded-xl h-11 text-[11px]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Tempo (Min)</label>
                                <input v-model="form.time_limit_minutes" type="number" class="w-full border-gray-200 rounded-xl h-11 text-sm text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-primary mb-1">XP</label>
                                <input v-model="form.base_xp" type="number" class="w-full border-primary/20 bg-primary/5 text-primary font-bold rounded-xl h-11 text-sm text-center">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <Link :href="route(user.role + '.classrooms.show', classroom.id)" class="text-[11px] font-black uppercase text-gray-400">Cancelar</Link>
                            <button type="submit" :disabled="form.processing" class="px-8 py-3 bg-primary text-white rounded-xl font-bold text-[11px] uppercase shadow-lg shadow-primary/20 transition-all disabled:opacity-50">
                                {{ form.processing ? 'Salvando...' : 'Salvar e Configurar Questões' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

// ✨ A LINHA QUE FALTAVA PARA NÃO DAR TELA BRANCA!
const route = window.route; 

const props = defineProps({
    classroom: Object
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const form = useForm({
    classroom_id: props.classroom?.id,
    type: 'task',
    title: '',
    description: '',
    base_xp: 100,
    start_date: '',
    end_date: '',
    time_limit_minutes: null,
    exam_settings: {
        multiple_choice: 0,
        descriptive: 0
    }
});

const submit = () => {
    form.post(route(user.value.role + '.activities.store'), {
        onSuccess: () => console.log('Criado com sucesso')
    });
};
</script>