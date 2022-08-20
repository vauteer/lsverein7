<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm} from "@inertiajs/inertia-vue3";
import Layout from '@/Shared/Layout.vue';
import TextInput from '@/Shared/TextInput.vue';
import AbortButton from '@/Shared/AbortButton.vue';
import SubmitButton from '@/Shared/SubmitButton.vue';
import DeleteButton from '@/Shared/DeleteButton.vue';


let props = defineProps({
    subscription: Object,
});

let form = useForm({
    name: '',
    amount: 0.0,
    transfer_text: '',
    memo: '',
});

let editMode = ref(false);

onMounted(() => {
    if (props.subscription !== undefined) {
        form.name = props.subscription.name;
        form.amount = props.subscription.amount;
        form.transfer_text = props.subscription.transfer_text;
        form.memo = props.subscription.memo;
        editMode.value = true;
    }
    document.getElementById('name').focus();
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/subscriptions/${props.subscription.id}`);
    } else {
        form.post('/subscriptions');
    }
};

let deleteSubscription = () => {
    if (confirm('Wollen Sie den Beitrag wirklich löschen ?')) {
        Inertia.delete(`/subscriptions/${props.subscription.id}`);
    }
};

const getTitle = computed(() => {
    return editMode.value ? "Beitrag bearbeiten" : "Neuen Beitrag";
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
                                    <TextInput class="sm:col-span-6" v-model="form.name" :error="form.errors.name"
                                               id="name"
                                               label="Name"/>
                                    <TextInput class="sm:col-span-2" v-model="form.amount" :error="form.errors.amount"
                                               id="amount" type="number" step="any" label="Betrag"/>
                                    <TextInput class="sm:col-span-6" v-model="form.transfer_text" :error="form.errors.transfer_text"
                                        id="transfer_text" label="Verwendungszweck" />
                                    <TextInput class="sm:col-span-6" v-model="form.memo" :error="form.errors.memo"
                                        id="memo" label="Memo" />
                                </div>
                                <div class="py-5">
                                    <div class="flex justify-between">
                                        <DeleteButton v-if="editMode" :onDelete="deleteSubscription"/>
                                        <div class="w-full flex justify-end">
                                            <AbortButton href="/subscriptions">
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
