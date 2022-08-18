<script setup>

let props = defineProps({
    disabled: Boolean,
});

</script>

<template>
    <button
        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 enabled:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-25"
        type="submit"
        :disabled="disabled"
    >
        <slot>Speichern</slot>
    </button>
</template>
