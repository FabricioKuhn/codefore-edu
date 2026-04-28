<template>
    <a v-if="blade" :href="href" :class="classes">
        <slot />
    </a>
    <Link v-else :href="href" :class="classes">
        <slot />
    </Link>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: String,
    active: Boolean,
    blade: { type: Boolean, default: false }, // <- Nova propriedade
});

const classes = computed(() => {
    return props.active
        ? // Mudou de font-black para font-bold
          "inline-flex items-center px-1 pt-1 border-b-4 border-primary text-sm font-bold text-secondary transition duration-150 ease-in-out"
        : // Mudou de font-bold para font-medium
          "inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-sm font-medium text-gray-500 hover:text-secondary hover:border-gray-300 transition duration-150 ease-in-out";
});
</script>
