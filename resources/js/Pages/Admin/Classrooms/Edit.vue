<template>
    <AppLayout>
        <Head title="Editar Turma" />

        <div class="py-6 min-h-screen">
            <div class="w-full max-w-[1600px] mx-auto px-5 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <nav
                        class="flex text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-1"
                    >
                        <ol
                            class="inline-flex items-center space-x-1 md:space-x-2"
                        >
                            <li>
                                <Link
                                    :href="route(user.role + '.dashboard')"
                                    class="hover:text-primary transition-colors"
                                    >Home</Link
                                >
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg
                                        class="w-3 h-3 text-gray-300 mx-1"
                                        fill="none"
                                        viewBox="0 0 6 10"
                                    >
                                        <path
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m1 9 4-4-4-4"
                                        />
                                    </svg>
                                    <Link
                                        :href="
                                            route(
                                                user.role + '.classrooms.index',
                                            )
                                        "
                                        class="hover:text-primary transition-colors"
                                        >Turmas</Link
                                    >
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg
                                        class="w-3 h-3 text-gray-300 mx-1"
                                        fill="none"
                                        viewBox="0 0 6 10"
                                    >
                                        <path
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m1 9 4-4-4-4"
                                        />
                                    </svg>
                                    <span class="text-gray-500"
                                        >Editar: {{ classroom.name }}</span
                                    >
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <div class="flex justify-between items-center">
                        <h2
                            class="font-bold text-2xl text-secondary leading-tight"
                        >
                            Editar Turma
                        </h2>

                        <button
                            v-if="user.role === 'admin' && classroom.is_active"
                            @click="inactivateClassroom"
                            class="text-[10px] font-bold text-amber-500 uppercase tracking-widest hover:text-amber-700 transition-colors"
                        >
                            <i class="fas fa-power-off mr-1"></i> Inativar Turma
                        </button>

                        <span
                            v-else-if="!classroom.is_active"
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-100 px-3 py-1 rounded-lg"
                        >
                            <i class="fas fa-info-circle mr-1"></i> Turma
                            Inativa
                        </span>
                    </div>
                </div>

                <form
                    @submit.prevent="submit"
                    class="w-full max-w-4xl mx-auto space-y-6"
                >
                    <div
                        class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100"
                    >
                        <h3
                            class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5"
                        >
                            Informações Básicas
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >Nome da Turma
                                    <span class="text-red-500">*</span></label
                                >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                    required
                                />
                                <div
                                    v-if="form.errors.name"
                                    class="text-red-500 text-xs mt-1 font-medium"
                                >
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >Disciplina
                                    <span class="text-red-500">*</span></label
                                >
                                <input
                                    v-model="form.subject"
                                    type="text"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                    required
                                />
                                <div
                                    v-if="form.errors.subject"
                                    class="text-red-500 text-xs mt-1 font-medium"
                                >
                                    {{ form.errors.subject }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >Professor Responsável
                                    <span class="text-red-500">*</span></label
                                >
                                <select
                                    v-model="form.teacher_id"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                    required
                                >
                                    <option
                                        v-for="teacher in teachers"
                                        :key="teacher.id"
                                        :value="teacher.id"
                                    >
                                        {{ teacher.name }}
                                    </option>
                                </select>
                            </div>

                            <div
                                class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2"
                            >
                                <div>
                                    <label
                                        class="block text-sm font-bold text-secondary mb-1"
                                        >XP Base Padrão</label
                                    >
                                    <input
                                        v-model="form.base_xp_level"
                                        type="number"
                                        class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-bold text-secondary mb-1"
                                        >Fator de Crescimento</label
                                    >
                                    <input
                                        v-model="form.level_growth_factor"
                                        type="number"
                                        step="0.01"
                                        class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100"
                    >
                        <h3
                            class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5"
                        >
                            Calendário e Frequência
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >Total de Aulas
                                    <span class="text-red-500">*</span></label
                                >
                                <input
                                    v-model="form.total_lessons"
                                    type="number"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                    required
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >Data de Início
                                    <span class="text-red-500">*</span></label
                                >
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                    required
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >% de Presença Mínima</label
                                >
                                <input
                                    v-model="form.min_attendance_percent"
                                    type="number"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >Frequência</label
                                >
                                <select
                                    v-model="form.frequency"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                >
                                    <option value="weekly">Semanal</option>
                                    <option value="biweekly">Quinzenal</option>
                                    <option value="daily">
                                        Diário (Seg a Sex)
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >Hora Início</label
                                >
                                <input
                                    v-model="form.start_time"
                                    type="time"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-secondary mb-1"
                                    >Hora Término</label
                                >
                                <input
                                    v-model="form.end_time"
                                    type="time"
                                    class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                />
                            </div>

                            <div class="md:col-span-3">
                                <label
                                    class="block text-sm font-bold text-secondary mb-3"
                                    >Dias da Semana</label
                                >
                                <div class="flex flex-wrap gap-3">
                                    <label
                                        v-for="(label, val) in daysMap"
                                        :key="val"
                                        class="inline-flex items-center px-4 py-2 border rounded-xl cursor-pointer transition-colors text-sm"
                                        :class="
                                            form.days_of_week.includes(val)
                                                ? 'border-primary bg-primary/5 text-primary font-bold'
                                                : 'border-gray-200 text-gray-500 hover:bg-gray-50'
                                        "
                                    >
                                        <input
                                            type="checkbox"
                                            :value="val"
                                            v-model="form.days_of_week"
                                            class="hidden"
                                        />
                                        {{ label }}
                                    </label>
                                </div>
                            </div>

                            <div class="md:col-span-3 pt-2">
                                <label
                                    class="inline-flex items-center cursor-pointer group"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="form.skip_holidays"
                                        class="w-5 h-5 rounded border-gray-300 text-primary shadow-sm focus:ring-primary"
                                    />
                                    <span
                                        class="ml-3 text-sm font-bold text-gray-600 group-hover:text-secondary transition-colors"
                                        >Pular Feriados na geração do
                                        calendário</span
                                    >
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-6">
                        <Link
                            :href="route(user.role + '.classrooms.index')"
                            class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-secondary transition-colors"
                        >
                            Cancelar
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-8 py-3 bg-primary border border-transparent rounded-xl font-bold text-[11px] text-white uppercase tracking-widest hover:bg-opacity-90 transition-all shadow-sm disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? "Salvando..."
                                    : "Atualizar Turma"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "../../../Layouts/AppLayout.vue";
import { Head, Link, useForm, usePage, router } from "@inertiajs/vue3";
import { computed } from "vue";

const route = window.route;

const props = defineProps({
    classroom: Object,
    teachers: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const daysMap = {
    1: "Segunda",
    2: "Terça",
    3: "Quarta",
    4: "Quinta",
    5: "Sexta",
    6: "Sábado",
    0: "Domingo",
};

// Carregamos o formulário já com os dados que vêm do banco
const form = useForm({
    name: props.classroom.name,
    subject: props.classroom.subject,
    teacher_id: props.classroom.teacher_id,
    base_xp_level: props.classroom.base_xp_level,
    level_growth_factor: props.classroom.level_growth_factor,
    total_lessons: props.classroom.total_lessons,
    start_date: props.classroom.start_date.split("T")[0], // Limpa a data para o formato do input
    min_attendance_percent: props.classroom.min_attendance_percent,
    frequency: props.classroom.frequency,
    days_of_week: props.classroom.days_of_week || [],
    start_time: props.classroom.start_time,
    end_time: props.classroom.end_time,
    skip_holidays: !!props.classroom.skip_holidays,
});

const submit = () => {
    form.put(route(user.value.role + ".classrooms.update", props.classroom.id));
};

const inactivateClassroom = () => {
    if (
        confirm(
            "Deseja realmente inativar esta turma? Ela não aparecerá mais nas listagens de turmas ativas.",
        )
    ) {
        // Mantemos o router.delete porque ele aponta para o método 'destroy' do Controller
        router.delete(
            route(user.value.role + ".classrooms.destroy", props.classroom.id),
        );
    }
};
</script>
