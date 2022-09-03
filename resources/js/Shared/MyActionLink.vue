<script setup>
import { Link } from '@inertiajs/inertia-vue3';

let props = defineProps({
    disabled: Boolean,
});

</script>
<template>
    <Link
        class="rounded-md border border-transparent py-2 px-4 bg-indigo-600 text-white shadow-sm enabled:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto disabled:opacity-25"
        as="button" type="button" :disabled="disabled">
        <slot />
    </Link>
</template>
