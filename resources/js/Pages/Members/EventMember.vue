<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm} from "@inertiajs/inertia-vue3";
import MyLayout from '@/Shared/MyLayout.vue';
import MySelect from '@/Shared/MySelect.vue';
import MyTextInput from '@/Shared/MyTextInput.vue';
import MyTextArea from '@/Shared/MyTextArea.vue';
import MyButton from '@/Shared/MyButton.vue';
import MyConfirmation from "@/Shared/MyConfirmation.vue";

let props = defineProps({
    origin: String,
    eventMember: Object,
    memberId: Number,
    events: Object,
});

let form = useForm({
    event_id: null,
    date: null,
    memo: null,
});

let showConfirmation = ref(false);
let editMode = ref(false);

onMounted(() => {
    if (props.eventMember !== undefined) {
        form.event_id = String(props.eventMember.event_id)
        form.date = props.eventMember.date;
        form.memo = props.eventMember.memo;

        editMode.value = true;
    }
    document.getElementById('date').focus();
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/members/${props.memberId}/event/${props.eventMember.id}/`);
    } else {
        form.post(`/members/${props.memberId}/event`);
    }
};

let deleteEventMember = () => {
    showConfirmation.value = false;
    Inertia.delete(`/members/${props.memberId}/event/${props.eventMember.id}`);
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
                                    <MyTextInput class="sm:col-span-2" v-model="form.date"
                                               :error="form.errors.date"
                                               id="date" type="date" label="Datum"/>
                                    <MySelect class="sm:col-span-4" v-model="form.event_id"
                                              :error="form.errors.event_id"
                                              :options="props.events" id="event" label="Ereignis"/>
                                    <MyTextArea class="sm:col-span-6" v-model="form.memo" :error="form.errors.memo"
                                                id="memo" label="Memo"/>
                                </div>
                                <div class="py-5">
                                    <div class="flex justify-between">
                                        <MyButton v-if="editMode" theme="danger" @click="showConfirmation = true"/>
                                        <div class="w-full flex justify-end">
                                            <MyButton theme="abort" @click="Inertia.get(origin)">Abbrechen</MyButton>
                                            <MyButton type="submit" class="ml-2" :disabled="form.processing">
                                                {{ getSubmitButtonText }}
                                            </MyButton>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <MyConfirmation v-if="showConfirmation" @canceled="showConfirmation = false" @confirmed="deleteEventMember">
            Ereignis löschen
        </MyConfirmation>
    </MyLayout>
</template>
