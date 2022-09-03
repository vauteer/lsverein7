<script setup>

let props = defineProps({
    id: String,
    type: {
        type: String,
        default: 'text',
    },
    step: {
        type: String,
        default: '1'
    },
    placeholder: {
        type: String,
        default: null,
    },
    regex: {
        type: String,
        default: null,
    },
    error: String,
    label: String,
    modelValue: [String, Number],
    autocomplete: String,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 ml-2" :for="id">{{ label }}</label>
        <input :id="id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
               :type="type" :step="step"
               :value="modelValue"
               :pattern="regex"
               :placeholder="placeholder"
               :autocomplete="autocomplete"
               @input="$emit('update:modelValue', $event.target.value)"
               :class="{'border-red-400': error}"
        />
        <div v-if="error" class="block text-xs font-medium text-red-500 mt-1">{{ error }}</div>
    </div>
</template>
