<script setup>
import { onMounted, ref } from 'vue';

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
    hideDisabled: Boolean,
    autofocus: Boolean,
});

defineEmits(['update:modelValue']);

const select = ref(null);

onMounted(() => {
    if (props.autofocus) {
        select.value.focus();
    }
});

</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 ml-2" :for="id">{{ label }}</label>
        <select :id="id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" :name="id" :disabled="disabled"
                :value="modelValue"
                :class="{'border-red-400': error, 'text-white': disabled && hideDisabled }"
                @change="$emit('update:modelValue', $event.target.value)"
                ref="select"
        >
            <option v-if="nullValue !== null" value="" v-text="nullValue" />
            <option v-for="option in options" :value="option.id" v-text="option.name" />
        </select>
        <div v-if="error" class="block text-xs font-medium text-red-500 mt-1">{{ error }}</div>
    </div>
</template>
