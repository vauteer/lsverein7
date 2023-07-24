<script setup>
import { computed, ref, onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import MyTextInput from '@/Shared/MyTextInput.vue';
import MyButton from '@/Shared/MyButton.vue';
import MyConfirmation from "@/Shared/MyConfirmation.vue";

let props = defineProps({
    origin: String,
    subscription: Object,
    deletable: Boolean,
    varDescription: String,
});

let form = useForm({
    name: '',
    amount: 0.0,
    transfer_text: '',
    memo: '',
});

onMounted(() => {
    if (props.subscription !== undefined) {
        form.name = props.subscription.name;
        form.amount = props.subscription.amount;
        form.transfer_text = props.subscription.transfer_text;
        form.memo = props.subscription.memo;
    }
});

let submit = () => {
    if (editMode.value) {
        form.put(`/subscriptions/${props.subscription.id}`);
    } else {
        form.post('/subscriptions');
    }
};

let showDeletion = ref(false);
let deleteEntity = () => {
    showDeletion.value = false;
    router.delete(`/subscriptions/${props.subscription.id}`);
};

const editMode = computed(() => props.subscription !== undefined);
const title = computed(() => editMode.value ? "Beitrag bearbeiten" : "Neuen Beitrag");
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
                            <MyTextInput class="sm:col-span-6" v-model="form.name" :error="form.errors.name"
                                         id="name" label="Name" autofocus/>
                            <MyTextInput class="sm:col-span-2" v-model="form.amount" :error="form.errors.amount"
                                         id="amount" type="number" step="any" label="Betrag"/>
                            <MyTextInput class="sm:col-span-6" v-model="form.transfer_text"
                                         :error="form.errors.transfer_text"
                                         id="transfer_text" label="Verwendungszweck" :placeholder="varDescription"/>
                            <MyTextInput class="sm:col-span-6" v-model="form.memo" :error="form.errors.memo"
                                         id="memo" label="Memo"/>
                        </div>
                        <div class="py-5">
                            <div class="flex justify-between">
                                <MyButton v-if="deletable" theme="danger" @click="showDeletion = true">
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
    <MyConfirmation v-if="showDeletion" @canceled="showDeletion = false" @confirmed="deleteEntity">
        {{ `Beitrag '${subscription.name}' löschen` }}
    </MyConfirmation>
</template>
