<template>
    <AppLayout>
        <Head title="Turmas" />

        <div class="py-6 min-h-screen">
            <div class="w-full max-w-[1600px] mx-auto px-5 sm:px-6 lg:px-8">
                <div class="mb-5">
                    <nav
                        class="flex text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-1"
                        aria-label="Breadcrumb"
                    >
                        <ol
                            class="inline-flex items-center space-x-1 md:space-x-2"
                        >
                            <li class="inline-flex items-center">
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
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
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
                                    <span class="text-gray-500">Turmas</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="font-bold text-xl text-secondary leading-tight">
                        Gestão de Turmas
                    </h2>
                </div>

                <div class="mb-5 flex justify-between items-center gap-4">
                    <div class="flex items-center">
                        <button
                            @click="toggleSearch"
                            class="p-2.5 bg-white border border-gray-100 shadow-sm rounded-xl text-gray-400 hover:text-primary hover:border-primary transition-all z-10 relative flex items-center justify-center w-10 h-10"
                            :class="{
                                'bg-primary/5 border-primary text-primary':
                                    isSearchOpen,
                            }"
                        >
                            <i class="fas fa-search"></i>
                        </button>

                        <transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 -translate-x-4 max-w-0"
                            enter-to-class="opacity-100 translate-x-0 max-w-[200px] sm:max-w-xs md:max-w-md"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 translate-x-0 max-w-[200px] sm:max-w-xs md:max-w-md"
                            leave-to-class="opacity-0 -translate-x-4 max-w-0"
                        >
                            <form
                                v-show="isSearchOpen"
                                @submit.prevent
                                class="relative ml-2 w-full overflow-hidden"
                            >
                                <input
                                    ref="searchInput"
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="ID, Turma, Matéria ou Prof..."
                                    class="w-full text-sm border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm pl-4 pr-10"
                                    @blur="handleBlur"
                                />
                                <button
                                    v-if="searchQuery"
                                    type="button"
                                    @click="clearSearch"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </transition>
                    </div>

                    <a
                        v-if="
                            user.role === 'admin' || user.role === 'super_admin'
                        "
                        :href="route(user.role + '.classrooms.create')"
                        class="inline-flex items-center px-6 py-2.5 bg-primary border border-transparent rounded-xl font-bold text-[11px] text-white uppercase tracking-widest hover:bg-opacity-90 transition-all shadow-sm whitespace-nowrap"
                    >
                        <i class="fas fa-plus mr-2 hidden sm:inline"></i>
                        <span class="sm:hidden"
                            ><i class="fas fa-plus"></i
                        ></span>
                        <span class="sm:inline">Nova Turma</span>
                    </a>
                </div>

                <div
                    class="mostra-no-desktop bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead
                                class="text-[10px] text-gray-400 uppercase tracking-widest bg-gray-50/80 border-b border-gray-100"
                            >
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-4 font-bold">
                                        Nome da Turma
                                    </th>
                                    <th scope="col" class="px-6 py-4 font-bold">
                                        Matéria
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-4 text-center font-bold"
                                    >
                                        Alunos
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-4 text-center font-bold"
                                    >
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-4 font-bold">
                                        Professor
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-4 text-right font-bold"
                                    >
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="!classrooms?.data?.length">
                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center text-gray-400 font-medium"
                                    >
                                        Nenhuma turma encontrada.
                                    </td>
                                </tr>

                                <tr
                                    v-for="classroom in classrooms.data"
                                    :key="classroom.id"
                                    class="transition-colors"
                                    :class="{
                                        'opacity-50 grayscale-[0.5] bg-gray-50/50':
                                            !classroom.is_active,
                                    }"
                                >
                                    <td
                                        class="px-6 py-4 font-bold text-gray-400 text-xs"
                                    >
                                        #{{
                                            String(classroom.id).padStart(
                                                4,
                                                "0",
                                            )
                                        }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            class="font-bold text-secondary text-sm"
                                        >
                                            {{ classroom.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-primary/10 text-primary text-[10px] font-bold px-3 py-1 rounded-lg uppercase tracking-wider"
                                        >
                                            {{ classroom.subject }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center font-bold text-secondary"
                                    >
                                        {{ classroom.students_count || 0 }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            :class="
                                                classroom.is_active
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-gray-200 text-gray-500'
                                            "
                                            class="text-[9px] font-bold px-2 py-1 rounded-md uppercase tracking-wider whitespace-nowrap"
                                        >
                                            {{
                                                classroom.is_active
                                                    ? "Ativa"
                                                    : "Inativa"
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm font-medium text-gray-600"
                                    >
                                        {{
                                            classroom.teacher?.name ||
                                            "Não Atribuído"
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right whitespace-nowrap"
                                    >
                                        <div
                                            class="flex justify-end gap-3 items-center"
                                        >
                                            <button
                                                @click="
                                                    handleToggleStatus(
                                                        classroom,
                                                    )
                                                "
                                                :class="
                                                    classroom.is_active
                                                        ? 'text-amber-500 hover:text-amber-700'
                                                        : 'text-green-500 hover:text-green-700'
                                                "
                                                class="transition-colors"
                                                :title="
                                                    classroom.is_active
                                                        ? 'Inativar'
                                                        : 'Reativar'
                                                "
                                            >
                                                <i
                                                    :class="
                                                        classroom.is_active
                                                            ? 'fas fa-power-off'
                                                            : 'fas fa-check-circle'
                                                    "
                                                    class="text-lg"
                                                ></i>
                                            </button>

                                            <a
                                                :href="
                                                    route(
                                                        user.role +
                                                            '.classrooms.show',
                                                        classroom.id,
                                                    )
                                                "
                                                class="text-blue-500 hover:text-blue-700 transition-colors"
                                                title="Gerenciar Turma"
                                            >
                                                <svg
                                                    class="w-5 h-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                    ></path>
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                    ></path>
                                                </svg>
                                            </a>
                                            <a
                                                v-if="
                                                    user.role === 'admin' ||
                                                    user.role === 'super_admin'
                                                "
                                                :href="
                                                    route(
                                                        user.role +
                                                            '.classrooms.edit',
                                                        classroom.id,
                                                    )
                                                "
                                                class="text-amber-500 hover:text-amber-600 transition-colors"
                                                title="Editar Turma"
                                            >
                                                <svg
                                                    class="w-5 h-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                    ></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mostra-no-mobile flex-col gap-5 mt-2">
                    <div
                        v-if="classrooms.data.length === 0"
                        class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center text-gray-400 font-medium"
                    >
                        Nenhuma turma encontrada.
                    </div>

                    <div
                        v-for="classroom in classrooms.data"
                        :key="classroom.id"
                        class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col gap-5"
                        :class="{
                            'opacity-60 grayscale-[0.5]': !classroom.is_active,
                        }"
                    >
                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <span
                                    class="text-[10px] font-bold text-gray-400 block mb-1"
                                    >#{{
                                        String(classroom.id).padStart(4, "0")
                                    }}</span
                                >
                                <h3
                                    class="font-bold text-secondary text-base leading-tight"
                                >
                                    {{ classroom.name }}
                                </h3>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span
                                    class="bg-primary/10 text-primary text-[9px] font-bold px-2.5 py-1.5 rounded-lg uppercase tracking-wider whitespace-nowrap"
                                >
                                    {{ classroom.subject }}
                                </span>
                                <span
                                    :class="
                                        classroom.is_active
                                            ? 'text-green-600'
                                            : 'text-gray-400'
                                    "
                                    class="text-[8px] font-bold uppercase tracking-widest"
                                >
                                    {{
                                        classroom.is_active
                                            ? "Ativa"
                                            : "Inativa"
                                    }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-3 bg-gray-50/80 p-4 rounded-xl border border-gray-200"
                        >
                            <div>
                                <span
                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-0.5"
                                    >Alunos</span
                                >
                                <span
                                    class="font-bold text-secondary text-sm"
                                    >{{ classroom.students_count || 0 }}</span
                                >
                            </div>
                            <div>
                                <span
                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-0.5"
                                    >Previstas</span
                                >
                                <span
                                    class="font-bold text-secondary text-sm"
                                    >{{ classroom.total_lessons || "-" }}</span
                                >
                            </div>
                            <div
                                class="col-span-2 pt-2 border-t border-gray-200 mt-1"
                            >
                                <span
                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-0.5"
                                    >Professor</span
                                >
                                <span class="font-bold text-gray-600 text-sm">{{
                                    classroom.teacher?.name || "Não Atribuído"
                                }}</span>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-1">
                            <button
                                @click="handleToggleStatus(classroom)"
                                class="flex-1 flex justify-center items-center gap-2 py-3 bg-white border border-gray-300 rounded-xl font-bold text-[11px] uppercase tracking-wider shadow-sm"
                                :class="
                                    classroom.is_active
                                        ? 'text-amber-500'
                                        : 'text-green-500'
                                "
                            >
                                <i
                                    :class="
                                        classroom.is_active
                                            ? 'fas fa-power-off'
                                            : 'fas fa-check-circle'
                                    "
                                ></i>
                                {{
                                    classroom.is_active
                                        ? "Inativar"
                                        : "Reativar"
                                }}
                            </button>
                            <a
                                :href="
                                    route(
                                        user.role + '.classrooms.show',
                                        classroom.id,
                                    )
                                "
                                class="flex-1 flex justify-center items-center gap-2 py-3 bg-white border border-gray-300 rounded-xl text-blue-600 font-bold text-[11px] uppercase tracking-wider shadow-sm"
                            >
                                <i class="fas fa-eye"></i> Abrir
                            </a>
                            <a
                                v-if="
                                    user.role === 'admin' ||
                                    user.role === 'super_admin'
                                "
                                :href="
                                    route(
                                        user.role + '.classrooms.edit',
                                        classroom.id,
                                    )
                                "
                                class="flex-1 flex justify-center items-center gap-2 py-3 bg-white border border-gray-300 rounded-xl text-amber-600 font-bold text-[11px] uppercase tracking-wider shadow-sm"
                            >
                                <i class="fas fa-pen"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>

                <div
                    v-if="classrooms.links && classrooms.links.length > 3"
                    class="mt-6 bg-white px-6 py-4 border border-gray-100 shadow-sm rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <div
                        class="text-[11px] font-medium text-gray-400 text-center sm:text-left w-full sm:w-auto"
                    >
                        Mostrando
                        <span class="font-bold">{{ classrooms.from }}</span> a
                        <span class="font-bold">{{ classrooms.to }}</span> de
                        <span class="font-bold">{{ classrooms.total }}</span>
                    </div>
                    <div class="flex flex-wrap justify-center gap-1">
                        <template
                            v-for="(link, index) in classrooms.links"
                            :key="index"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1.5 text-[11px] font-bold rounded-lg border transition-colors',
                                    link.active
                                        ? 'bg-primary border-primary text-white'
                                        : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50',
                                ]"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="px-3 py-1.5 text-[11px] font-bold rounded-lg border border-transparent text-gray-300"
                            ></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "../../../Layouts/AppLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { ref, computed, nextTick } from "vue";
import { watch } from "vue";
import debounce from "lodash/debounce";

const route = window.route;

const props = defineProps({
    classrooms: Object,
    filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const searchQuery = ref(props.filters?.search || "");
const isSearchOpen = ref(!!props.filters?.search);
const searchInput = ref(null);

const toggleSearch = async () => {
    isSearchOpen.value = !isSearchOpen.value;

    if (isSearchOpen.value) {
        await nextTick();
        setTimeout(() => {
            if (searchInput.value) searchInput.value.focus();
        }, 100);
    } else if (searchQuery.value) {
        clearSearch();
    }
};

watch(
    searchQuery,
    debounce((value) => {
        router.get(
            route(user.value.role + ".classrooms.index"),
            { search: value },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        );
    }, 300)
);

// ✅ FUNÇÃO PARA ALTERNAR STATUS (INATIVAR/REATIVAR)
const handleToggleStatus = (classroom) => {
    const acao = classroom.is_active ? "inativar" : "reativar";
    if (
        confirm(
            `Deseja realmente ${acao} a turma "${classroom.name}" e todos os seus conteúdos?`,
        )
    ) {
        router.delete(
            route(user.value.role + ".classrooms.destroy", classroom.id),
            {
                preserveScroll: true,
            },
        );
    }
};

const clearSearch = () => {
    searchQuery.value = "";
    isSearchOpen.value = false;
};

const handleBlur = () => {
    if (!searchQuery.value) {
        isSearchOpen.value = false;
    }
};
</script>

<style scoped>
@media (min-width: 1024px) {
    .mostra-no-mobile {
        display: none !important;
    }
}

@media (max-width: 1023px) {
    .mostra-no-desktop {
        display: none !important;
    }
    .mostra-no-mobile {
        display: flex !important;
    }
}
</style>
