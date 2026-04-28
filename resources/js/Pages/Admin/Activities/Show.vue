<template>
    <AppLayout>
        <Head :title="`Gerenciar - ${activity.title}`" />

        <div class="py-12 min-h-screen">
            <div class="max-w-[1600px] mx-auto px-6 lg:px-8">
                
                <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <nav class="flex text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-2">
                            <ol class="inline-flex items-center space-x-2">
                                <li><Link :href="route(user.role + '.dashboard')" class="hover:text-primary transition-colors">Home</Link></li>
                                <li class="flex items-center">
                                    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
                                    <Link :href="route(user.role + '.classrooms.index')" class="hover:text-primary transition-colors">Turmas</Link>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
                                    <Link :href="route(user.role + '.classrooms.show', activity.classroom_id)" class="hover:text-primary transition-colors">{{ activity.classroom?.name || 'Turma' }}</Link>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
                                    <span class="text-gray-500">{{ activity.title }}</span>
                                </li>
                            </ol>
                        </nav>
                        <h2 class="text-2xl font-bold text-secondary flex items-center gap-3">
                            <span class="bg-secondary text-white px-3 py-1 rounded text-[10px] uppercase font-black tracking-widest">
                                {{ activity.type === 'exam' ? 'Prova' : 'Tarefa' }}
                            </span>
                            {{ activity.title }}
                        </h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <button @click="showImportModal = true" class="px-5 py-2.5 bg-white border border-gray-200 text-secondary rounded-xl font-bold text-[11px] uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">
                            <i class="fas fa-database mr-2 text-primary"></i> Vincular do Banco
                        </button>
                        <Link :href="route(user.role + '.questions.create')" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-[11px] uppercase tracking-widest hover:opacity-90 transition-all shadow-sm shadow-primary/20">
                            <i class="fas fa-plus mr-2"></i> Nova Questão
                        </Link>
                    </div>
                </div>

                <div class="mb-6 bg-white shadow-sm sm:rounded-2xl border border-gray-100 p-8 relative">
    
    

    <div v-if="!isEditing">
        <div class="flex flex-col lg:flex-row justify-between items-start mb-6">
            <div class="max-w-3xl">
                <div class="flex items-center gap-4">
                    <h3 class="text-xl font-black text-secondary">{{ activity.title }}</h3>
                    <button @click="startEditing" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-primary transition-colors flex items-center gap-1.5 mt-0.5 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                        <i class="fas fa-pen"></i> Editar Configurações
                    </button>
                </div>
                <p class="text-gray-500 mt-2 text-sm font-medium leading-relaxed">{{ activity.description || 'Sem descrição fornecida.' }}</p>
            </div>
            
            <div class="mt-4 lg:mt-0 flex gap-4 shrink-0">
                <div class="text-center bg-gray-50 px-6 py-4 rounded-xl border border-gray-100">
                    <span class="text-[10px] uppercase text-gray-400 font-black tracking-widest block mb-1">XP Base</span>
                    <div class="text-2xl font-black text-primary">{{ activity.base_xp }}</div>
                </div>
                <div v-if="activity.time_limit_minutes" class="text-center bg-gray-50 px-6 py-4 rounded-xl border border-gray-100">
                    <span class="text-[10px] uppercase text-gray-400 font-black tracking-widest block mb-1">Tempo Limite</span>
                    <div class="text-2xl font-black text-secondary">{{ activity.time_limit_minutes }}m</div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50/50 p-5 rounded-xl border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap gap-12 w-full">
                <div>
                    <span class="text-[10px] uppercase font-black text-gray-400 tracking-widest block mb-1">Status</span>
                    <span :class="statusColor(activity.status)" class="text-sm font-bold uppercase">{{ statusLabel(activity.status) }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-black text-gray-400 tracking-widest block mb-1">Início</span>
                    <span class="text-sm font-bold text-secondary">{{ formatDateTime(activity.start_date) }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-black text-gray-400 tracking-widest block mb-1">Prazo Final</span>
                    <span class="text-sm font-bold text-secondary">{{ formatDateTime(activity.end_date) }}</span>
                </div>
            </div>
        </div>
    </div>
   

    <form v-else @submit.prevent="saveActivity" class="animate-fade-in-up">
        <h3 class="text-lg font-black text-secondary uppercase tracking-widest mb-6 border-b border-gray-100 pb-4">Editar Avaliação</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Título</label>
                <input v-model="editForm.title" type="text" class="w-full border-gray-200 rounded-xl h-11 text-sm focus:ring-primary focus:border-primary" required>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Instruções</label>
                <textarea v-model="editForm.description" rows="3" class="w-full border-gray-200 rounded-xl text-sm focus:ring-primary focus:border-primary"></textarea>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Status</label>
                <select v-model="editForm.status" class="w-full border-gray-200 rounded-xl h-11 text-sm focus:ring-primary focus:border-primary font-bold text-secondary">
                    <option value="draft">Rascunho</option>
                    <option value="active">Ativa</option>
                    <option value="in_progress">Em Andamento</option>
                    <option value="closed">Encerrada</option>
                    <option value="canceled">Cancelada</option>
                </select>
            </div>

            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-[10px] font-black uppercase text-primary mb-1">XP Base</label>
                    <input v-model="editForm.base_xp" type="number" class="w-full border-primary/30 bg-primary/5 rounded-xl h-11 text-sm font-bold text-primary focus:ring-primary focus:border-primary" required>
                </div>
                <div class="w-1/2">
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Tempo (Min)</label>
                    <input v-model="editForm.time_limit_minutes" type="number" placeholder="Livre" class="w-full border-gray-200 rounded-xl h-11 text-sm focus:ring-primary focus:border-primary">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Data/Hora Início</label>
                <input v-model="editForm.start_date" type="datetime-local" class="w-full border-gray-200 rounded-xl h-11 text-[11px] focus:ring-primary focus:border-primary">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Data/Hora Final</label>
                <input v-model="editForm.end_date" type="datetime-local" class="w-full border-gray-200 rounded-xl h-11 text-[11px] focus:ring-primary focus:border-primary">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <button type="button" @click="cancelEditing" class="px-6 py-2 text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">Cancelar</button>
            <button type="submit" :disabled="editForm.processing" class="px-8 py-3 bg-primary text-white rounded-xl font-bold text-[11px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all disabled:opacity-50">
                {{ editForm.processing ? 'Salvando...' : 'Salvar Alterações' }}
            </button>
        </div>
    </form>
</div>

                <div v-if="activity.type === 'exam'" class="bg-purple-50 border border-purple-100 rounded-xl p-5 mb-6 flex flex-wrap gap-6 text-sm items-center">
                    <div><strong class="text-purple-800 font-black uppercase tracking-widest text-[10px]">Sorteio Definido:</strong></div>
                    <div class="text-purple-700 font-bold"><i class="fas fa-check-circle mr-1"></i> {{ activity.exam_settings?.multiple_choice || 0 }} Múltipla Escolha</div>
                    <div class="text-purple-700 font-bold"><i class="fas fa-pen mr-1"></i> {{ activity.exam_settings?.descriptive || 0 }} Descritivas</div>
                    <div class="text-[10px] text-purple-500 ml-auto pt-1 font-black uppercase tracking-widest">(Os alunos receberão aleatoriamente desta quantidade)</div>
                </div>

                <div class="flex items-center justify-between mb-4 mt-8 px-2">
                    <h3 class="text-lg font-black text-secondary uppercase tracking-widest">
                        {{ activity.type === 'exam' ? 'Pool de Questões (Sorteio)' : 'Questões da Tarefa' }}
                    </h3>
                </div>

                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden mb-12">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ordem</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Enunciado</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tipo</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Peso Nesta Prova</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="(question, index) in activity.questions" :key="question.id" class="hover:bg-gray-50/30 transition">
                                <td class="px-6 py-4 font-black text-gray-300 text-xs">{{ index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-secondary line-clamp-2" v-html="question.statement"></div>
                                    <div class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mt-2">ID Banco: #{{ question.id }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="question.type === 'multiple_choice' ? 'bg-blue-50 text-blue-500' : 'bg-purple-50 text-purple-500'" class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest">
                                        {{ question.type === 'multiple_choice' ? 'Múltipla Escolha' : 'Descritiva' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form @submit.prevent="updateWeight(question)" class="flex items-center justify-center gap-2">
                                        <input type="number" v-model="weightForms[question.id]" class="w-16 text-center text-xs font-bold border-gray-200 rounded py-1 px-2 focus:ring-primary focus:border-primary" min="1">
                                        <button type="submit" class="text-[10px] text-gray-400 hover:text-primary uppercase font-black">OK</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="detachQuestion(question.id)" class="text-red-400 hover:text-red-600 font-black text-[10px] uppercase tracking-widest transition">
                                        Remover
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!activity.questions || activity.questions.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-gray-400 font-bold text-sm mb-4">Nenhuma questão vinculada a esta avaliação ainda.</p>
                                    <button @click="showImportModal = true" class="text-primary hover:underline text-[10px] font-black uppercase tracking-widest">Vincular do Banco</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
<div class="mt-12 pt-8 border-t border-gray-100 flex justify-center pb-12">
            <Link 
                :href="route(user.role + '.classrooms.show', activity.classroom_id)" 
                class="flex items-center gap-3 px-10 py-4 bg-gray-800 text-white rounded-xl font-bold text-[11px] uppercase tracking-widest hover:bg-gray-700 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-md"
            >
                <i class="fas fa-arrow-left text-[10px]"></i>
                Voltar para a Turma
            </Link>
        </div>
                </div>
                
        </div>

        <Modal :show="showImportModal" @close="showImportModal = false" max-width="4xl">
            <div class="bg-white px-8 py-6 flex justify-between items-center border-b border-gray-100">
                <h3 class="text-xl font-black text-secondary uppercase tracking-widest">Vincular do Banco</h3>
                <button @click="showImportModal = false" class="text-gray-400 hover:text-red-500 transition-colors w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-8 bg-gray-50/30">
                <div class="mb-6">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" v-model="searchQuery" placeholder="Pesquisar por enunciado..." class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl bg-white focus:ring-1 focus:ring-primary focus:border-primary text-sm shadow-sm transition-all">
                    </div>
                </div>

                <form @submit.prevent="attachSelectedQuestions">
                    <div class="max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar bg-white border border-gray-100 rounded-xl overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 sticky top-0 backdrop-blur-sm z-10 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 w-10"></th>
                                    <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Enunciado</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tipo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="q in filteredQuestions" :key="q.id" @click="toggleSelection(q.id)" class="hover:bg-blue-50/30 cursor-pointer transition">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" :value="q.id" v-model="importForm.question_ids" @click.stop class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded cursor-pointer">
                                    </td>
                                    <td class="px-4 py-4 text-xs font-black text-gray-300">#{{ q.id }}</td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-bold text-secondary line-clamp-1" v-html="q.statement"></div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span :class="q.type === 'multiple_choice' ? 'bg-blue-50 text-blue-500' : 'bg-purple-50 text-purple-500'" class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest">
                                            {{ q.type === 'multiple_choice' ? 'Múltipla' : 'Descritiva' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="filteredQuestions.length === 0">
                                    <td colspan="4" class="px-4 py-12 text-center text-gray-400 font-bold text-sm">
                                        Nenhuma questão encontrada para a busca.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="showImportModal = false" class="px-6 py-2 text-[11px] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition">Cancelar</button>
                        <button type="submit" :disabled="importForm.processing || importForm.question_ids.length === 0" class="px-8 py-3 bg-primary text-white rounded-xl font-bold text-[11px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all disabled:opacity-50">
                            {{ importForm.processing ? 'Vinculando...' : `Vincular Selecionadas (${importForm.question_ids.length})` }}
                        </button>
                    </div>
                </form>
                
            </div>
        </Modal>

    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, Link, useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed, reactive } from "vue";

const route = window.route;
const props = defineProps({
    activity: Object,
    availableQuestions: Array
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// Controle de Modais
const showImportModal = ref(false);

// Busca do Banco de Questões
const searchQuery = ref('');
const filteredQuestions = computed(() => {
    if (!searchQuery.value) return props.availableQuestions;
    const s = searchQuery.value.toLowerCase();
    return props.availableQuestions.filter(q => q.statement.toLowerCase().includes(s));
});

// Formulário de Importação
const importForm = useForm({
    question_ids: []
});

const toggleSelection = (id) => {
    const index = importForm.question_ids.indexOf(id);
    if (index === -1) {
        importForm.question_ids.push(id);
    } else {
        importForm.question_ids.splice(index, 1);
    }
};

const attachSelectedQuestions = () => {
    importForm.post(route(user.value.role + '.activities.questions.attach', props.activity.id), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
        }
    });
};

const isEditing = ref(false);

const editForm = useForm({
    type: props.activity.type, // O controller exige o type na validação
    title: props.activity.title,
    description: props.activity.description || '',
    base_xp: props.activity.base_xp,
    time_limit_minutes: props.activity.time_limit_minutes || null,
    status: props.activity.status,
    // O datetime-local espera o formato YYYY-MM-DDThh:mm, então pegamos os 16 primeiros caracteres
    start_date: props.activity.start_date ? props.activity.start_date.substring(0, 16) : '',
    end_date: props.activity.end_date ? props.activity.end_date.substring(0, 16) : '',
    exam_settings: props.activity.exam_settings || { multiple_choice: 0, descriptive: 0 }
});

const startEditing = () => {
    isEditing.value = true;
};

const cancelEditing = () => {
    editForm.reset();
    isEditing.value = false;
};

const saveActivity = () => {
    editForm.put(route(user.value.role + '.activities.update', props.activity.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        }
    });
};

// Formulários Independentes para atualizar o peso de cada questão
const weightForms = reactive({});
// Preenche reativamente os pesos atuais
props.activity.questions?.forEach(q => {
    weightForms[q.id] = q.pivot?.weight_override || q.default_weight;
});

const updateWeight = (question) => {
    router.patch(route(user.value.role + '.activities.questions.update_weight', [props.activity.id, question.id]), {
        weight: weightForms[question.id]
    }, {
        preserveScroll: true
    });
};

const detachQuestion = (questionId) => {
    if(confirm('Tem certeza que deseja remover esta questão da avaliação?')) {
        router.delete(route(user.value.role + '.activities.questions.detach', [props.activity.id, questionId]), {
            preserveScroll: true
        });
    }
};

// Helpers de formatação
const formatDateTime = (d) => {
    if (!d) return 'Não definido';
    const date = new Date(d);
    return date.toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const statusLabel = (status) => {
    const map = { draft: 'Rascunho', active: 'Ativa', closed: 'Encerrada', in_progress: 'Em Andamento' };
    return map[status] || status;
};

const statusColor = (status) => {
    if (status === 'active') return 'text-green-500';
    if (status === 'draft') return 'text-gray-400';
    return 'text-secondary';
};
</script>