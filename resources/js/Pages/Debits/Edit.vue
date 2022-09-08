<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm} from "@inertiajs/inertia-vue3";
import MyLayout from '@/Shared/MyLayout.vue';
import MySelect from '@/Shared/MySelect.vue';
import MyTextInput from '@/Shared/MyTextInput.vue';
import MyAbortButton from '@/Shared/MyAbortButton.vue';
import MySubmitButton from '@/Shared/MySubmitButton.vue';
import MyDeleteButton from '@/Shared/MyDeleteButton.vue';
import MyConfirmation from "@/Shared/MyConfirmation.vue";

let props = defineProps({
    origin: String,
    debit: Object,
    members: Object,
    today: String,
    varDescription: String,
    deletable: Boolean,
});

let form = useForm({
    member_id: null,
    amount: 0.0,
    transfer_text: '',
    due_at: props.today,
});

let showDeleteConfirmation = ref(false);
let editMode = ref(false);

onMounted(() => {
    if (props.debit !== undefined) {
        form.member_id = String(props.debit.member_id);
        form.amount = props.debit.amount;
        form.transfer_text = props.debit.transfer_text;
        form.due_at = props.debit.due_at;

        editMode.value = true;
    }
    document.getElementById('member').focus();
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/debits/${props.debit.id}`);
    } else {
        form.post('/debits');
    }
};

let deleteDebit = () => {
    showDeleteConfirmation.value = false;
    Inertia.delete(`/debits/${props.debit.id}`);
};

const getTitle = computed(() => {
    return editMode.value ? "Lastschrift bearbeiten" : "Neue Lastschrift";
});

const getMySubmitButtonText = computed(() => {
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
                                    <MySelect class="sm:col-span-4" v-model="form.member_id"
                                              :error="form.errors.member_id"
                                              :options="props.members" id="member" label="Mitglied"/>
                                    <MyTextInput class="sm:col-span-2" v-model="form.amount" :error="form.errors.amount"
                                                 id="amount" type="number" step="0.01" label="Betrag"/>
                                    <MyTextInput class="sm:col-span-6" v-model="form.transfer_text" :error="form.errors.transfer_text"
                                                 id="transfer_text" label="Verwendungszweck" :placeholder="varDescription"/>
                                    <MyTextInput class="sm:col-span-2" v-model="form.due_at"
                                                 :error="form.errors.due_at"
                                                 id="due-at" type="date" label="Fällig am"/>
                                </div>
                                <div class="py-5">
                                    <div class="flex justify-between">
                                        <MyDeleteButton v-if="deletable" @click.prevent="showDeleteConfirmation = true" />
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
        <MyConfirmation v-if="showDeleteConfirmation" @canceled="showDeleteConfirmation = false" @confirmed="deleteDebit">
            {{ `Lastschrift '${debit.name}' löschen`}}
        </MyConfirmation>
    </MyLayout>
</template>
