<template>
    <AppLayout>
        <Head title="Dashboard Admin" />

        <div class="py-6 bg-gray-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="mb-6 p-6 bg-white rounded-[2rem] border border-gray-100 shadow-sm flex items-center justify-between"
                >
                    <div>
                        <h2 class="text-2xl font-bold text-secondary">
                            Dashboard da Instituição
                        </h2>
                        <p class="text-gray-500 font-medium mt-1">
                            Visão geral do {{ tenant.name }}
                        </p>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
                >
                    <div
                        class="bg-white rounded-[2rem] shadow-sm p-6 border border-gray-100"
                    >
                        <h3
                            class="text-gray-400 text-[11px] font-bold uppercase tracking-wider"
                        >
                            Turmas Ativas
                        </h3>
                        <p class="text-3xl font-bold text-secondary mt-2">
                            {{ totalTurmas }}
                        </p>
                    </div>
                    <div
                        class="bg-white rounded-[2rem] shadow-sm p-6 border border-gray-100"
                    >
                        <h3
                            class="text-gray-400 text-[11px] font-bold uppercase tracking-wider"
                        >
                            Total Alunos
                        </h3>
                        <p class="text-3xl font-bold text-secondary mt-2">
                            {{ totalAlunos }}
                        </p>
                    </div>
                    <div
                        class="bg-white rounded-[2rem] shadow-sm p-6 border border-gray-100"
                    >
                        <h3
                            class="text-gray-400 text-[11px] font-bold uppercase tracking-wider"
                        >
                            XP Gerada
                        </h3>
                        <p class="text-3xl font-bold text-primary mt-2">
                            +{{ Number(xpGerada).toLocaleString("pt-BR") }}
                        </p>
                    </div>
                    <div
                        class="bg-white rounded-[2rem] shadow-sm p-6 border border-gray-100"
                    >
                        <h3
                            class="text-gray-400 text-[11px] font-bold uppercase tracking-wider"
                        >
                            Novos Alunos
                        </h3>
                        <p class="text-3xl font-bold text-secondary mt-2">
                            {{ novosAlunos }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div
                        class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm p-8 border border-gray-100"
                    >
                        <div class="flex items-center justify-between mb-6">
                            <h3
                                class="text-lg font-bold text-secondary flex items-center"
                            >
                                <i
                                    class="far fa-calendar-alt mr-3 text-primary"
                                ></i>
                                Agenda Geral
                            </h3>
                        </div>
                        <FullCalendar :options="calendarOptions" />
                    </div>

                    <div class="lg:col-span-1">
                        <div
                            class="bg-white rounded-[2rem] shadow-sm p-8 border border-gray-100"
                        >
                            <h3
                                class="text-lg font-bold text-secondary mb-6 flex items-center"
                            >
                                <i
                                    class="fas fa-clipboard-check mr-3 text-primary"
                                ></i>
                                Por Corrigir
                            </h3>
                            <div class="space-y-4">
                                <div
                                    v-if="correcoesPendentes.length === 0"
                                    class="text-center py-6"
                                >
                                    <p
                                        class="text-gray-400 font-medium uppercase text-xs tracking-widest"
                                    >
                                        Tudo em dia!
                                    </p>
                                </div>

                                <div
                                    v-else
                                    v-for="atv in correcoesPendentes"
                                    :key="atv.id"
                                    class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100 transition-all group"
                                >
                                    <div
                                        class="flex justify-between items-center mb-4"
                                    >
                                        <span
                                            class="text-[10px] font-bold uppercase text-primary bg-primary/10 px-3 py-1.5 rounded-xl"
                                        >
                                            {{ atv.classroom.name }}
                                        </span>
                                        <span
                                            class="bg-secondary text-white text-[11px] font-bold px-2.5 py-1 rounded-lg"
                                        >
                                            {{ atv.submissions_count }}
                                        </span>
                                    </div>
                                    <h4
                                        class="font-bold text-secondary text-sm mb-5 leading-tight uppercase"
                                    >
                                        {{ atv.title }}
                                    </h4>
                                    <Link
                                        :href="`/professor/activities/${atv.id}/submissions`"
                                        class="flex items-center justify-center w-full py-3.5 bg-white border border-gray-200 rounded-2xl text-[11px] font-bold text-gray-500 hover:bg-primary hover:text-white transition-all shadow-sm"
                                    >
                                        ABRIR CORREÇÕES
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "../../Layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import tippy from "tippy.js";
import "tippy.js/dist/tippy.css";
import "tippy.js/themes/light-border.css";

const props = defineProps({
    tenant: Object,
    totalTurmas: Number,
    totalAlunos: Number,
    xpGerada: [Number, String],
    novosAlunos: Number,
    correcoesPendentes: Array,
    eventos: Array,
});

const calendarOptions = {
    plugins: [dayGridPlugin],
    initialView: "dayGridMonth",
    locale: "pt-br",
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "",
    },
    events: props.eventos,

    eventDidMount: function (info) {
        tippy(info.el, {
            content: `
                <div class="p-1 text-center font-sans">
                    <div class="text-[10px] uppercase opacity-50 mb-1 font-bold">
                        ${info.event.start.toLocaleDateString("pt-BR")}
                    </div>
                    <div class="text-sm font-bold mb-1">${info.event.title}</div>
                    <hr class="border-gray-200 my-2">
                    <div class="text-[11px] font-medium text-gray-600">${info.event.extendedProps.description || ""}</div>
                </div>
            `,
            allowHTML: true,
            placement: "top",
            theme: "light-border",
            animation: "shift-away",
        });
    },

    eventClick: function (info) {
        if (info.event.url) {
            info.jsEvent.preventDefault();
            window.location.href = info.event.url;
        }
    },
    height: "auto",
    displayEventTime: false,
};
</script>

<style>
/* Estilização FullCalendar */
.fc-toolbar-title {
    color: var(--secondary-color) !important;
    font-weight: 800 !important;
    text-transform: uppercase;
    font-size: 1.1rem !important;
}
.fc-button-primary {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    font-size: 11px !important;
    border-radius: 10px !important;
    padding: 8px 14px !important;
}
.fc-event {
    cursor: pointer;
    border: none !important;
    padding: 5px 10px !important;
    border-radius: 10px !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}
.fc-day-today {
    background: rgba(0, 173, 154, 0.03) !important;
}
.fc-col-header-cell-cushion {
    color: #9ca3af;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    text-decoration: none !important;
}
.fc-daygrid-day-number {
    color: var(--secondary-color);
    font-weight: 700;
    text-decoration: none !important;
    padding: 8px !important;
    font-size: 13px;
}
</style>
