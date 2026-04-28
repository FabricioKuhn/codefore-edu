<template>
    <div class="min-h-screen bg-gray-50/50" :style="themeColors">
        <nav
            class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50"
        >
            <div class="max-w-[96%] mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex gap-6">
                        <div class="shrink-0 flex items-center">
                            <Link
                                :href="homeRoute"
                                class="flex items-center font-black text-2xl text-primary tracking-tighter"
                            >
                                <img
                                    v-if="tenant?.logo_original"
                                    :src="`/storage/${tenant.logo_original}`"
                                    alt="Logo do Cliente"
                                    class="h-9 w-auto object-contain"
                                />

                                <img
                                    v-else
                                    src="/logo-codeforce-02.png"
                                    alt="CodeForce"
                                    class="h-9 w-auto object-contain"
                                />
                            </Link>
                        </div>

                        <div
                            class="hidden sm:-my-px sm:flex pl-8 lg:pl-10 gap-6"
                        >
                            <template v-if="user.role === 'admin'">
                                <NavLink
                                    :href="route('admin.dashboard')"
                                    :active="route().current('admin.dashboard')"
                                >
                                    Dashboard
                                </NavLink>

                                <NavLink
                                    :href="route('admin.classrooms.index')"
                                    :active="
                                        route().current('admin.classrooms.*')
                                    "
                                    blade
                                >
                                    Turmas
                                </NavLink>
                                <NavLink
                                    :href="route('admin.students.index')"
                                    :active="
                                        route().current('admin.students.*')
                                    "
                                    blade
                                >
                                    Alunos
                                </NavLink>
                                <NavLink
                                    :href="route('admin.teachers.index')"
                                    :active="
                                        route().current('admin.teachers.*')
                                    "
                                    blade
                                >
                                    Professores
                                </NavLink>
                            </template>

                            <template v-if="user.role === 'teacher'">
                                <NavLink
                                    :href="route('teacher.dashboard')"
                                    :active="
                                        route().current('teacher.dashboard')
                                    "
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('teacher.classrooms.index')"
                                    :active="
                                        route().current('teacher.classrooms.*')
                                    "
                                >
                                    Minhas Turmas
                                </NavLink>
                                <NavLink
                                    :href="route('teacher.students.index')"
                                    :active="
                                        route().current('teacher.students.*')
                                    "
                                >
                                    Meus Alunos
                                </NavLink>
                                <NavLink
                                    :href="route('teacher.questions.index')"
                                    :active="
                                        route().current('teacher.questions.*')
                                    "
                                >
                                    Banco de Questões
                                </NavLink>
                            </template>

                            <template v-if="user.role === 'student'">
                                <NavLink
                                    :href="route('student.dashboard')"
                                    :active="
                                        route().current('student.dashboard')
                                    "
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('profile.edit')"
                                    :active="route().current('profile.*')"
                                >
                                    Meu Perfil
                                </NavLink>
                                <NavLink
                                    :href="route('student.classrooms.index')"
                                    :active="
                                        route().current('student.classrooms.*')
                                    "
                                >
                                    Minha Turma
                                </NavLink>
                                <NavLink
                                    :href="route('student.feed')"
                                    :active="route().current('student.feed')"
                                >
                                    Feed
                                </NavLink>
                            </template>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="relative">
                            <button
                                v-if="userMenuOpen"
                                @click="userMenuOpen = false"
                                class="fixed inset-0 h-full w-full cursor-default z-40"
                            ></button>

                            <button
                                @click="userMenuOpen = !userMenuOpen"
                                class="relative z-50 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                            >
                                <div class="flex items-center gap-2">
                                    <img
                                        v-if="user.avatar"
                                        :src="`/storage/${user.avatar}`"
                                        alt="Avatar"
                                        class="h-8 w-8 rounded-full object-cover border border-gray-200 shadow-sm"
                                    />
                                    <img
                                        v-else
                                        :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&color=00ad9a&background=E5F7F5&rounded=true`"
                                        alt="Avatar"
                                        class="h-8 w-8 rounded-full border border-gray-200 shadow-sm"
                                    />
                                    <span class="font-medium">{{
                                        user.name
                                    }}</span>
                                </div>
                                <div class="ml-1">
                                    <svg
                                        class="fill-current h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </button>

                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <div
                                    v-show="userMenuOpen"
                                    class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 origin-top-right"
                                >
                                    <div class="py-1">
                                        <a
                                            :href="route('profile.edit')"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                        >
                                            Meu Perfil
                                        </a>

                                        <a
                                            v-if="user.role === 'admin'"
                                            href="#"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                        >
                                            Assinatura
                                        </a>

                                        <Link
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors border-t border-gray-100"
                                        >
                                            Sair
                                        </Link>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>

                    <div class="-mr-2 flex items-center sm:hidden">
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out"
                        >
                            <svg
                                class="h-6 w-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    v-if="!mobileMenuOpen"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    v-else
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-show="mobileMenuOpen"
                class="sm:hidden bg-white border-b border-gray-200"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <template v-if="user.role === 'admin'">
                        <ResponsiveNavLink
                            :href="route('admin.dashboard')"
                            :active="route().current('admin.dashboard')"
                            >Dashboard</ResponsiveNavLink
                        >
                        <ResponsiveNavLink
                            :href="route('admin.classrooms.index')"
                            :active="route().current('admin.classrooms.*')"
                            >Turmas</ResponsiveNavLink
                        >
                        <ResponsiveNavLink
                            :href="route('admin.students.index')"
                            :active="route().current('admin.students.*')"
                            >Alunos</ResponsiveNavLink
                        >
                        <ResponsiveNavLink
                            :href="route('admin.teachers.index')"
                            :active="route().current('admin.teachers.*')"
                            >Professores</ResponsiveNavLink
                        >
                    </template>

                    <template v-if="user.role === 'teacher'">
                        <ResponsiveNavLink
                            :href="route('teacher.dashboard')"
                            :active="route().current('teacher.dashboard')"
                            >Dashboard</ResponsiveNavLink
                        >
                        <ResponsiveNavLink
                            :href="route('teacher.classrooms.index')"
                            :active="route().current('teacher.classrooms.*')"
                            >Minhas Turmas</ResponsiveNavLink
                        >
                        <ResponsiveNavLink
                            :href="route('teacher.students.index')"
                            :active="route().current('teacher.students.*')"
                            >Meus Alunos</ResponsiveNavLink
                        >
                    </template>

                    <template v-if="user.role === 'student'">
                        <ResponsiveNavLink
                            :href="route('student.dashboard')"
                            :active="route().current('student.dashboard')"
                            >Dashboard</ResponsiveNavLink
                        >
                        <ResponsiveNavLink
                            :href="route('profile.edit')"
                            :active="route().current('profile.*')"
                            >Meu Perfil</ResponsiveNavLink
                        >
                        <ResponsiveNavLink
                            :href="route('student.classrooms.index')"
                            :active="route().current('student.classrooms.*')"
                            >Minha Turma</ResponsiveNavLink
                        >
                        <ResponsiveNavLink
                            :href="route('student.feed')"
                            :active="route().current('student.feed')"
                            >Feed</ResponsiveNavLink
                        >
                    </template>
                </div>

                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">
                            {{ user.name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">
                            {{ user.email }}
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')"
                            >Meu Perfil</ResponsiveNavLink
                        >
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full text-left block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out"
                        >
                            Sair
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import NavLink from "./NavLink.vue";
import ResponsiveNavLink from "./ResponsiveNavLink.vue";

const route = window.route;

// Gerenciamento de estado dos menus (O equivalente ao Alpine.js)
const userMenuOpen = ref(false);
const mobileMenuOpen = ref(false);

// Pegamos as propriedades globais enviadas pelo Laravel
const page = usePage();
const user = computed(() => page.props.auth.user);
const tenant = computed(() => page.props.auth.tenant);

// Define a rota inicial com base no papel do usuário
const homeRoute = computed(() => {
    if (user.value.role === "admin") return route("admin.dashboard");
    if (user.value.role === "teacher") return route("teacher.dashboard");
    if (user.value.role === "student") return route("student.dashboard");
    return "/";
});

// Define as cores CSS dinamicamente
const themeColors = computed(() => {
    return {
        "--primary-color": tenant.value?.primary_color || "#00ad9a",
        "--secondary-color": tenant.value?.secondary_color || "#333333",
        "--tertiary-color": tenant.value?.tertiary_color || "#ffffff",
    };
});
</script>

<style>
/* CSS Helpers utilizando as variáveis globais do sistema */
.text-primary {
    color: var(--primary-color) !important;
}
.text-secondary {
    color: var(--secondary-color) !important;
}
.bg-primary {
    background-color: var(--primary-color) !important;
}
.bg-secondary {
    background-color: var(--secondary-color) !important;
}
.border-primary {
    border-color: var(--primary-color) !important;
}
</style>
