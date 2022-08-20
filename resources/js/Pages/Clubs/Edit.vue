<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import { useForm, Head, Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Shared/Layout.vue";
import TextInput from "@/Shared/TextInput.vue";
import CheckBox from "@/Shared/CheckBox.vue";
import MySelect from "@/Shared/MySelect.vue";
import AbortButton from "@/Shared/AbortButton.vue";
import SubmitButton from "@/Shared/SubmitButton.vue";
import DeleteButton from "@/Shared/DeleteButton.vue";
import ImageUpload from "@/Shared/ImageUpload.vue";

let props = defineProps({
    club: Object,
    displayStyles: Object,
});

let form = useForm({
    name: '',
    street: '',
    zipcode: '',
    city: '',
    blsv_member: false,
    bank: '',
    account_owner: '',
    iban: '',
    bic: '',
    sepa: '',
    sepa_date: null,
    logo: '',
    display: '1',
});

onMounted(() => {
    if (props.club !== undefined) {
        form.name = props.club.name;
        form.street = props.club.street;
        form.zipcode = props.club.zipcode;
        form.city = props.club.city;
        form.blsv_member = props.club.blsv_member;
        form.bank = props.club.bank;
        form.account_owner = props.club.account_owner;
        form.iban = props.club.iban;
        form.bic = props.club.bic;
        form.sepa = props.club.sepa;
        form.display = String(props.club.display);
        form.logo = props.club.logo;

        editMode.value = true;
    }
    document.getElementById('name').focus();
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/clubs/${props.club.id}`);
    } else {
        form.post('/clubs');
    }
};

let deleteEntity = () => {
    if (confirm('Wollen Sie den Verein wirklich löschen ?')) {
        Inertia.delete(`/clubs/${props.club.id}`);
    }
};

let editMode = ref(false);

const getTitle = computed(() => {
    return editMode.value ? "Verein bearbeiten" : "Neuer Verein";
});

const getSubmitButtonText = computed(() => {
    return editMode.value ? "Speichern" : "Hinzufügen";
});

const getLogoUrl = computed(() => {
    return form.logo ? '/storage/logo/' + form.logo : null;
})

function onLogoChanged(filename) {
    form.logo = filename;
}
</script>

<template>
    <Head title="Vereine" />
    <Layout>
        <button
            tabindex="-1"
            class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
        ></button>
        <div class="relative z-30 w-full max-w-3xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
            <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
                <div class="font-medium text-lg text-gray-900 ml-3 mb-4">{{ getTitle }}</div>

                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                    <form  @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                        <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                            <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-8">
                                <ImageUpload
                                    label="Logo"
                                    class="sm:col-span-2"
                                    id="logo"
                                    :image-url="getLogoUrl"
                                    location="logo"
                                    resize-height="150"
                                    v-on:change="onLogoChanged"
                                />
                                <div class="sm:col-span-6 grid sm:grid-cols-6 gap-y-4">
                                    <TextInput class="sm:col-span-6" v-model="form.name" :error="form.errors.name" id="name"
                                               label="Name"/>
                                    <TextInput class="sm:col-span-6" v-model="form.street" :error="form.errors.street" id="street"
                                               label="Straße"/>
                                    <TextInput class="sm:col-span-1" v-model="form.zipcode" :error="form.errors.zipcode"
                                        id="zipcode" label="PLZ" />
                                    <TextInput class="sm:col-span-5 sm:ml-3" v-model="form.city" :error="form.errors.city"
                                        id="city" label="Ort" />
                                    <TextInput class="sm:col-span-6" v-model="form.bank" :error="form.errors.bank"
                                        id="bank" label="Bankinstitut" />
                                    <TextInput class="sm:col-span-6" v-model="form.account_owner" :error="form.errors.account_owner"
                                        id="account_owner" label="Kontoinhaber" />
                                    <TextInput class="sm:col-span-6" v-model="form.iban" :error="form.errors.iban"
                                        id="iban" label="IBAN" />
                                    <TextInput class="sm:col-span-6" v-model="form.bic" :error="form.errors.bic"
                                        id="bic" label="BIC" />
                                    <TextInput class="sm:col-span-4" v-model="form.sepa" :error="form.errors.sepa"
                                        id="sepa" label="Sepa-Mandat" />
                                    <TextInput class="sm:col-span-2 sm:ml-3" v-model="form.sepa_date" :error="form.errors.sepa_date" id="sepa_date"
                                               type='date' label="Sepa-Mandatsdatum" />
                                    <CheckBox class="sm:col-span-6" v-model="form.blsv_member" :error="form.errors.blsv_member"
                                              id="blsv_member" label="BLSV-Mitglied"/>
                                    <MySelect class="sm:col-span-6" v-model="form.display" :error="form.errors.display"
                                              :options="props.displayStyles" id="display" label="Anzeige"/>
                                </div>
                            </div>
                            <div class="py-5">
                                <div class="flex justify-between">
                                    <DeleteButton v-if="editMode" :onDelete="deleteEntity"/>
                                    <div class="w-full flex justify-end">
                                        <AbortButton href="/clubs">
                                            Abbrechen
                                        </AbortButton>
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
    </Layout>
</template>
