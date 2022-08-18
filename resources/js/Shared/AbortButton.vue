<script setup>
import { Link } from '@inertiajs/inertia-vue3';

let props = defineProps({
    disabled: Boolean,
});

</script>
<template>
    <Link
        as="button" type="button"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 enabled:hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-25"
        :disabled="disabled"
    >
        <slot>Abbrechen</slot>
    </Link>
</template>
