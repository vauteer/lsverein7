<script setup>
let props = defineProps({
    id: String,
    error: String,
    label: String,
    modelValue: String,
    options: Object,
    nullValue: {
        type: String,
        default: null,
    },
    disabled: {
        type: [Boolean, Function]
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 ml-2" :for="id">{{ label }}</label>
        <select :id="id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" :name="id" :disabled="disabled"
                :value="modelValue"
                :class="{'border-red-400': error}"
                @change="$emit('update:modelValue', $event.target.value)"
        >
            <option v-if="nullValue !== null" value="" v-text="nullValue" />
            <option v-for="(value, key) in options" :value="key" v-text="value" />
        </select>
        <div v-if="error" class="block text-xs font-medium text-red-500 mt-1">{{ error }}</div>
    </div>
</template>
