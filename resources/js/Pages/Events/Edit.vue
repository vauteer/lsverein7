<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import MyLayout from '@/Shared/MyLayout.vue';
import MyTextInput from '@/Shared/MyTextInput.vue';
import MyCheckBox from "@/Shared/MyCheckBox.vue";
import MyAbortButton from '@/Shared/MyAbortButton.vue';
import MySubmitButton from '@/Shared/MySubmitButton.vue';
import MyDeleteButton from '@/Shared/MyDeleteButton.vue';


let props = defineProps({
    origin: String,
    event: Object,
});

let form = useForm({
    name: '',
    global: false,
});

const user = computed(() => usePage().props.value.auth.user);
let editMode = ref(false);

onMounted(() => {
    if (props.event !== undefined) {
        form.name = props.event.name;
        form.global = props.event.club_id === null;

        editMode.value = true;
    }
    document.getElementById('name').focus();
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/events/${props.event.id}`);
    } else {
        form.post('/events');
    }
};

let deleteEvent = () => {
    if (confirm('Wollen Sie das Ereignis wirklich löschen ?')) {
        Inertia.delete(`/events/${props.event.id}`);
    }
};

const getTitle = computed(() => {
    return editMode.value ? "Ereignis bearbeiten" : "Neues Ereignis";
});

const getSubmitButtonText = computed(() => {
    return editMode.value ? "Speichern" : "Hinzufügen";
});

</script>

<template>
    <MyLayout>
        <div>
            <button
                tabindex="-1"
                class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
            ></button>
            <div
                class="relative z-30 w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
                <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
                    <div class="font-medium text-lg text-gray-900 ml-3 mb-4">{{ getTitle }}</div>

                    <div
                        class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                        <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                            <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                                <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                                    <MyTextInput class="sm:col-span-6" v-model="form.name" :error="form.errors.name"
                                               id="name"
                                               label="Name"/>
                                    <MyCheckBox v-if="user.admin" v-model="form.global" :error="form.errors.global"
                                        id="global" label="Global" />
                                </div>
                                <div class="py-5">
                                    <div class="flex justify-between">
                                        <MyDeleteButton v-if="editMode" :onDelete="deleteEvent"/>
                                        <div class="w-full flex justify-end">
                                            <MyAbortButton :href="origin" />
                                            <MySubmitButton class="ml-2" :disabled="form.processing">
                                                {{ getSubmitButtonText }}
                                            </MySubmitButton>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MyLayout>
</template>
