<template>
    <a v-if="blade" :href="href" :class="classes"><slot /></a>
    <Link v-else :href="href" :class="classes"><slot /></Link>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: String,
    active: Boolean,
    blade: { type: Boolean, default: false },
});

const classes = computed(() => {
    return props.active
        ? // Mudou de font-black para font-bold
          "block w-full pl-3 pr-4 py-2 border-l-4 border-primary text-left text-base font-bold text-primary bg-primary/10 transition duration-150 ease-in-out"
        : // Mudou de font-bold para font-medium
          "block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-500 hover:text-secondary hover:bg-gray-50 transition duration-150 ease-in-out";
});
</script>
