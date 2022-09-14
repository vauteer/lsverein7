<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import MyLayout from '@/Shared/MyLayout.vue';
import MyTextInput from '@/Shared/MyTextInput.vue';
import MySelect from "@/Shared/MySelect.vue";
import MyConfirmation from "@/Shared/MyConfirmation.vue";
import MyButton from "@/Shared/MyButton.vue";

let props = defineProps({
    origin: String,
    section: Object,
    deletable: Boolean,
    blsvSections: Object,
});

let form = useForm({
    name: '',
    blsv_id: null,
});

const user = computed(() => usePage().props.value.auth.user);
let editMode = ref(false);

onMounted(() => {
    if (props.section !== undefined) {
        form.name = props.section.name;
        form.blsv_id = String(props.section.blsv_id),

            editMode.value = true;
    }
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/sections/${props.section.id}`);
    } else {
        form.post('/sections');
    }
};

let showDeletion = ref(false);
let deleteEntity = () => {
    showDeletion.value = false;
    Inertia.delete(`/sections/${props.section.id}`);
};

const title = computed(() => {
    return editMode.value ? "Abteilung bearbeiten" : "Neue Abteilung";
});

const submitButtonText = computed(() => {
    return editMode.value ? "Speichern" : "Hinzufügen";
});

</script>

<template>
    <MyLayout>
        <button
            tabindex="-1"
            class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
        ></button>
        <div
            class="relative z-30 w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
            <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
                <div class="font-medium text-lg text-gray-900 ml-3 mb-4">{{ title }}</div>

                <div
                    class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                    <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                        <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                            <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                                <MyTextInput class="sm:col-span-6" v-model="form.name" :error="form.errors.name"
                                             id="name" label="Name" autofocus/>
                                <MySelect class="sm:col-span-6" v-model="form.blsv_id" :error="form.errors.blsv_id"
                                          :options="props.blsvSections" id="blsv-id" label="BLSV-Zuordnung"
                                          nullValue="(BLSV-Sparte)"
                                />
                            </div>
                            <div class="py-5">
                                <div class="flex justify-between">
                                    <MyButton v-if="deletable" theme="danger" @click="showDeletion=true">
                                        Löschen
                                    </MyButton>
                                    <div class="w-full flex justify-end">
                                        <MyButton theme="abort" @click="Inertia.get(origin)">Abbrechen</MyButton>
                                        <MyButton type="submit" class="ml-2" :disabled="form.processing">
                                            {{ submitButtonText }}
                                        </MyButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <MyConfirmation v-if="showDeletion" @canceled="showDeletion = false" @confirmed="deleteEntity">
                {{ `Abteilung '${section.name}' löschen` }}
            </MyConfirmation>
        </div>
    </MyLayout>
</template>
