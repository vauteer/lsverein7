<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm} from "@inertiajs/inertia-vue3";
import Layout from '@/Shared/Layout.vue';
import MySelect from '@/Shared/MySelect.vue';
import TextInput from '@/Shared/TextInput.vue';
import MyTextArea from '@/Shared/MyTextArea.vue';
import AbortButton from '@/Shared/AbortButton.vue';
import SubmitButton from '@/Shared/SubmitButton.vue';
import DeleteButton from '@/Shared/DeleteButton.vue';


let props = defineProps({
    origin: String,
    memberSubscription: Object,
    memberId: Number,
    subscriptions: Object,
});

let form = useForm({
    subscription_id: null,
    from: null,
    to: null,
    memo: null,
});

let editMode = ref(false);

onMounted(() => {
    if (props.memberSubscription !== undefined) {
        form.subscription_id = String(props.memberSubscription.subscription_id)
        form.memo = props.memberSubscription.memo;

        editMode.value = true;
    }
    document.getElementById('subscription').focus();
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/members/${props.memberId}/subscription/${props.memberSubscription.id}/`);
    } else {
        form.post(`/members/${props.memberId}/subscription`);
    }
};

let deletememberSubscription = () => {
    if (confirm('Beitrag löschen ?')) {
        Inertia.delete(`/members/${props.memberId}/subscription/${props.memberSubscription.id}`);
    }
};

const getTitle = computed(() => {
    return editMode.value ? "Beitrag bearbeiten" : "Neuer Beitrag";
});

const getSubmitButtonText = computed(() => {
    return editMode.value ? "Speichern" : "Hinzufügen";
});

</script>

<template>
    <Layout>
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
                                    <MySelect class="sm:col-span-6" v-model="form.subscription_id"
                                              :error="form.errors.subscription_id"
                                              :options="props.subscriptions" id="subscription" label="Beitrag"/>
                                    <MyTextArea class="sm:col-span-6" v-model="form.memo" :error="form.errors.memo"
                                                id="memo" label="Memo"/>
                                </div>
                                <div class="py-5">
                                    <div class="flex justify-between">
                                        <DeleteButton v-if="editMode" :onDelete="deletememberSubscription"/>
                                        <div class="w-full flex justify-end">
                                            <AbortButton :href="origin" />
                                            <SubmitButton class="ml-2" :disabled="form.processing">
                                                {{ getSubmitButtonText }}
                                            </SubmitButton>
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
