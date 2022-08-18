<script setup>
import { computed, ref, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useForm, Head } from "@inertiajs/inertia-vue3";
import Layout from '@/Shared/Layout.vue';
import EditTitle from '@/Shared/EditTitle.vue'
import TextInput from '@/Shared/TextInput.vue';
import AbortButton from '@/Shared/AbortButton.vue';
import SubmitButton from '@/Shared/SubmitButton.vue';
import DeleteButton from '@/Shared/DeleteButton.vue';
import CheckBox from '@/Shared/CheckBox.vue';

let props = defineProps({
    user: Object,
});

let form = useForm({
    name: '',
    email: '',
    admin: false,
});

let editMode = ref(false);

onMounted(() => {
    if (props.user !== undefined) {
        form.name = props.user.name;
        form.email = props.user.email;
        form.admin = props.user.admin;

        editMode.value = true;
    }

    document.getElementById('name').focus();
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/users/${props.user.id}`);
    } else {
        form.post('/users');
    }
};

let deleteUser = () => {
    if (confirm('Wollen Sie den Benutzer wirklich löschen ?')) {
        Inertia.delete(`/users/${props.user.id}`);
    }
};

const getTitle = computed(() => {
    return editMode.value ? "Benutzer bearbeiten" : "Neuer Benutzer";
});

const getSubmitButtonText = computed(() => {
    return editMode.value ? "Speichern" : "Hinzufügen";
});

</script>

<template>
    <Head title="Benutzer" />
    <Layout>
        <div>
            <button
                tabindex="-1"
                class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
            ></button>
            <div class="relative z-30 w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
                <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
                    <EditTitle class="ml-3 mb-4">{{ getTitle }}</EditTitle>

                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                        <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                            <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                                <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                                    <TextInput class="sm:col-span-6" v-model="form.name" :error="form.errors.name" id="name"
                                               label="Name"/>
                                    <TextInput class="sm:col-span-6" v-model="form.email" :error="form.errors.email" id="email"
                                               label="Email"/>
                                    <div class="sm:col-span-6">
                                        <CheckBox v-model="form.admin" :error="form.errors.admin"
                                                  id="admin" label="Admin"/>
                                    </div>
                                </div>
                                <div class="py-5">
                                    <div class="flex justify-between">
                                        <DeleteButton v-if="editMode" :onDelete="deleteUser"/>
                                        <div class="w-full flex justify-end">
                                            <AbortButton :href="route('users')">
                                                Abbrechen
                                            </AbortButton>
                                            <SubmitButton class="ml-2" :title="getSubmitButtonText"
                                                          :disabled="form.processing"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
