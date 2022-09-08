<script setup>
import { computed, ref, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useForm, Head } from "@inertiajs/inertia-vue3";
import MyLayout from '@/Shared/MyLayout.vue';
import MyTextInput from '@/Shared/MyTextInput.vue';
import MyAbortButton from '@/Shared/MyAbortButton.vue';
import MySubmitButton from '@/Shared/MySubmitButton.vue';
import MyDeleteButton from '@/Shared/MyDeleteButton.vue';
import MySelect from '@/Shared/MySelect.vue';
import MyConfirmation from "@/Shared/MyConfirmation.vue";

let props = defineProps({
    origin: String,
    user: Object,
    deletable: Boolean,
    roles: Object,
});

let form = useForm({
    name: '',
    email: '',
    role: '1',
});

let showDeleteConfirmation = ref(false);
let editMode = ref(false);

onMounted(() => {
    if (props.user !== undefined) {
        form.name = props.user.name;
        form.email = props.user.email;
        form.role = String(props.user.role);

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
    showDeleteConfirmation.value = false;
    Inertia.delete(`/users/${props.user.id}`);
};

const getTitle = computed(() => {
    return editMode.value ? "Benutzer bearbeiten" : "Neuer Benutzer";
});

const getMySubmitButtonText = computed(() => {
    return editMode.value ? "Speichern" : "Hinzufügen";
});

</script>

<template>
    <Head title="Benutzer" />
    <MyLayout>
        <div>
            <button
                tabindex="-1"
                class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
            ></button>
            <div class="relative z-30 w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
                <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
                    <div class="font-medium text-lg text-gray-900 ml-3 mb-4">{{ getTitle }}</div>

                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                        <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                            <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                                <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                                    <MyTextInput class="sm:col-span-6" v-model="form.name" :error="form.errors.name" id="name"
                                               label="Name"/>
                                    <MyTextInput class="sm:col-span-6" v-model="form.email" :error="form.errors.email" id="email"
                                               label="Email"/>
                                    <MySelect class="sm:col-span-6" v-model="form.role" :error="form.errors.role"
                                              :options="props.roles" id="role" label="Rolle"/>
                                </div>
                                <div class="py-5">
                                    <div class="flex justify-between">
                                        <MyDeleteButton v-if="deletable" @click.prevent="showRestoreConfirmation = true" />
                                        <div class="w-full flex justify-end">
                                            <MyAbortButton :href="origin" />
                                            <MySubmitButton class="ml-2" :disabled="form.processing">
                                                {{ getMySubmitButtonText }}
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
        <MyConfirmation v-if="showDeleteConfirmation" @canceled="showRestoreConfirmation = false" @confirmed="deleteUser">
            {{ `Benutzer '${user.name}' löschen`}}
        </MyConfirmation>
    </MyLayout>
</template>
