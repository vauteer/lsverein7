<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm} from "@inertiajs/inertia-vue3";
import MySelect from '@/Shared/MySelect.vue';
import MyTextArea from '@/Shared/MyTextArea.vue';
import MyButton from '@/Shared/MyButton.vue';
import MyConfirmation from "@/Shared/MyConfirmation.vue";

let props = defineProps({
    origin: String,
    memberSubscription: Object,
    memberId: Number,
    subscriptions: Object,
});

let form = useForm({
    subscription_id: null,
    memo: null,
});

let editMode = ref(false);
let showDeletion = ref(false);

onMounted(() => {
    if (props.memberSubscription !== undefined) {
        form.subscription_id = String(props.memberSubscription.subscription_id)
        form.memo = props.memberSubscription.memo;

        editMode.value = true;
    }
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/members/${props.memberId}/subscription/${props.memberSubscription.id}/`);
    } else {
        form.post(`/members/${props.memberId}/subscription`);
    }
};

let deleteMemberSubscription = () => {
    showDeletion.value = false;
    Inertia.delete(`/members/${props.memberId}/subscription/${props.memberSubscription.id}`);
};

const title = computed(() => {
    return editMode.value ? "Beitrag bearbeiten" : "Neuer Beitrag";
});

const submitButtonText = computed(() => {
    return editMode.value ? "Speichern" : "Hinzufügen";
});

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
                            <MySelect class="sm:col-span-6" v-model="form.subscription_id"
                                      :error="form.errors.subscription_id"
                                      :options="props.subscriptions" id="subscription" label="Beitrag" autofocus/>
                            <MyTextArea class="sm:col-span-6" v-model="form.memo" :error="form.errors.memo"
                                        id="memo" label="Memo"/>
                            {{ form.subscription_id }}
                        </div>
                        <div class="py-5">
                            <div class="flex justify-between">
                                <MyButton v-if="editMode" theme="danger" @click="showDeletion=true">
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
    </div>
    <MyConfirmation v-if="showDeletion" @canceled="showDeletion = false" @confirmed="deleteMemberSubscription">
        Beitrag löschen
    </MyConfirmation>
</template>
