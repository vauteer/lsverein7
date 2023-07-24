<script setup>
import { computed, ref, onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import MyTextInput from '@/Shared/MyTextInput.vue';
import MyTextArea from '@/Shared/MyTextArea.vue';
import MyListbox from '@/Shared/MyListbox.vue';
import MyButton from '@/Shared/MyButton.vue';
import MyConfirmation from "@/Shared/MyConfirmation.vue";

let props = defineProps({
    origin: String,
    memberSection: Object,
    memberId: Number,
    sections: Array,
});

let form = useForm({
    section_id: null,
    from: null,
    to: null,
    memo: null,
});

let showDeletion = ref(false);

onMounted(() => {
    if (props.memberSection !== undefined) {
        form.section_id = props.memberSection.section_id;
        form.from = props.memberSection.from;
        form.to = props.memberSection.to;
        form.memo = props.memberSection.memo;
    }
});

let submit = () => {
    if (editMode.value) {
        form.put(`/members/${props.memberId}/section/${props.memberSection.id}/`);
    } else {
        form.post(`/members/${props.memberId}/section`);
    }
};

let deleteMemberSection = () => {
    showDeletion.value = false;
    router.delete(`/members/${props.memberId}/section/${props.memberSection.id}`);
};

const editMode = computed(() => props.memberSection !== undefined);
const title = computed(() => editMode.value ? "Spartenzugehörigkeit bearbeiten" : "Neue Spartenzugehörigkeit");
const submitButtonText = computed(() => editMode.value ? "Speichern" : "Hinzufügen");

</script>

<template>
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
                            <MyListbox class="sm:col-span-6" v-model="form.section_id"
                                      :error="form.errors.section_id"
                                      :options="props.sections" id="section" label="Sparte" autofocus/>
                            <MyTextInput class="sm:col-span-3" v-model="form.from"
                                         :error="form.errors.from"
                                         id="from" type="date" label="Von"/>
                            <MyTextInput class="sm:col-span-3" v-model="form.to"
                                         :error="form.errors.to"
                                         id="to" type="date" label="Bis"/>
                            <MyTextArea class="sm:col-span-6" v-model="form.memo" :error="form.errors.memo"
                                        id="memo" label="Memo"/>
                        </div>
                        <div class="py-5">
                            <div class="flex justify-between">
                                <MyButton v-if="editMode" theme="danger" @click="showDeletion=true">
                                    Löschen
                                </MyButton>
                                <div class="w-full flex justify-end">
                                    <MyButton theme="abort" @click="router.get(origin)">Abbrechen</MyButton>
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
    </div>
    <MyConfirmation v-if="showDeletion" @canceled="showDeletion = false" @confirmed="deleteMemberSection">
        Abteilungzugehörigkeit löschen
    </MyConfirmation>
</template>
