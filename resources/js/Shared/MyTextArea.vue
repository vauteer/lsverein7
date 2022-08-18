<script setup>

let props = defineProps({
    id: String,
    placeholder: {
        type: String,
        default: null,
    },
    rows: {
        type: String,
        default: '2'
    },
    error: String,
    label: String,
    modelValue: [String]
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 ml-2" :for="id">{{ label }}</label>
        <textarea :id="id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
               :value="modelValue"
               :placeholder="placeholder"
               @input="$emit('update:modelValue', $event.target.value)"
               :class="{'border-red-400': error}" :rows="rows"
        />
        <div v-if="error" class="block text-xs font-medium text-red-500 mt-1">{{ error }}</div>
    </div>
</template>
