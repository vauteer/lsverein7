<script setup>
import { computed, onMounted } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    id: String,
    options: Array,
    modelValue: [String, Number, Array],
    placeholder: {
        type: String,
        default: 'Option auswählen',
    },
    label: String,
    nullOption: String,
    nullable: Boolean,
    multiple: Boolean,
    autofocus: Boolean,
    error: String,
    disabled: {
        type: [Boolean, Function]
    },
    optionsUp: {
        type: Boolean,
        default: false,
    },
    lowHeight: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['update:modelValue']);

const buttonText = computed(() => {
    return props.options.filter(option => {
        if (Array.isArray(props.modelValue)) {
            return props.modelValue.includes(option.id);
        }

        return props.modelValue === option.id;
    }).map(option => option.name).join(', ');
});

onMounted(() => {
    if (props.nullOption) {
        props.options.unshift({ id: null, name: props.nullOption });
    }

    if (props.autofocus && props.id) {
        document.getElementById(props.id)?.focus();
    }
});

</script>

<template>
    <Listbox
        as="div" :multiple="multiple" :disabled="disabled"
        :modelValue="modelValue"
        @update:modelValue="value => emit('update:modelValue', value)">
        <ListboxLabel v-if="label" class="block text-sm font-medium text-gray-700 ml-2">{{ label }}</ListboxLabel>
        <div class="relative">
            <ListboxButton class="relative w-full cursor-default rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm" :id="id">
                <span class="block truncate" v-if="buttonText">{{ buttonText }}</span>
                <span v-else class="text-gray-500">{{ placeholder }}</span>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </span>
            </ListboxButton>

            <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <ListboxOptions class="absolute z-40 mt-1 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                :class="[lowHeight ? 'max-h-28' : 'max-h-60', { 'bottom-full' : optionsUp }]"
                >
                    <ListboxOption as="template" v-for="option in props.options" :key="option.name" :value="option.id" v-slot="{ active, selected }">
                        <li :class="[active ? 'text-white bg-indigo-600' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                            <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{ option.name }}</span>

                            <span v-if="selected" :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                <CheckIcon class="h-5 w-5" aria-hidden="true" />
              </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
            <div v-if="error" class="block text-xs font-medium text-red-500 mt-1">{{ error }}</div>
        </div>
    </Listbox>
</template>
