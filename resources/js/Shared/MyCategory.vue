<script setup>
import { Link } from '@inertiajs/vue3';

let props =defineProps({
    search: {
        type: Boolean,
        default: true,
    },
    modelValue: String,
    createUrl: String,
    buttonDisabled: {
        type: [Boolean, Object],
    },
    buttonTitle: {
        type: String,
        default: 'Neu',
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="sm:flex items-center justify-between">
        <div class="w-full text-2xl font-medium text-gray-900"><slot /></div>
        <div class="flex justify-between">
            <input v-if="search" type="text" placeholder="Suchen..." class="text-gray-700 px-2 mr-4 my-2 text-base border rounded-lg"
                   @input="$emit('update:modelValue', $event.target.value)" :value="modelValue"
            />
            <div v-if="createUrl">
                <Link
                    class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 my-2 mr-2 text-sm font-medium text-white shadow-sm enabled:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto disabled:opacity-25"
                    :href="createUrl" as="button" type="button" :disabled="buttonDisabled">
                    {{ buttonTitle }}
                </Link>
            </div>
        </div>
    </div>
</template>
