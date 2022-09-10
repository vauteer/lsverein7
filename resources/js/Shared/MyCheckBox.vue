<script setup>
let props = defineProps({
    id: String,
    label: String,
    error: String,
    modelValue: {
        type: [Array, Boolean],
        default: false,
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <div class="flex">
            <input :id="id" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mt-1"
                   type="checkbox"
                   :checked="modelValue"
                   @change="$emit('update:modelValue', $event.target.checked)"
            />
            <label class="block text-sm font-medium text-gray-700 ml-2" :for="id">{{ label }}</label>
        </div>
        <div v-if="error" class="block text-xs font-medium text-red-500 mt-1">{{ error }}</div>
    </div>
</template>
