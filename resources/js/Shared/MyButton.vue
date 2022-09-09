<script setup>
import {computed} from "vue";

let props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    disabled: Boolean,
    theme: String,
});

let themeClasses = computed(() => {
    if (props.theme === 'danger')
        return ['text-white', 'bg-red-500', 'enabled:hover:bg-red-700', 'focus:ring-red-500']
    if (props.theme === 'abort')
        return ['text-gray-700', 'bg-white', 'enabled:hover:bg-gray-50', 'focus:ring-indigo-500']
    else
        return ['text-white', 'bg-indigo-600', 'enabled:hover:bg-indigo-700', 'focus:ring-indigo-500']
});

</script>

<template>
    <button :type="type" :disabled="disabled"
        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-25"
            :class="themeClasses"
    >
        <slot />
    </button>
</template>
