<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import { useForm, Head, Link } from "@inertiajs/inertia-vue3";
import MyTextInput from "@/Shared/MyTextInput.vue";
import MyCheckBox from "@/Shared/MyCheckBox.vue";
import MyListbox from "@/Shared/MyListbox.vue";
import MyButton from "@/Shared/MyButton.vue";
import MyImageUpload from "@/Shared/MyImageUpload.vue";
import MyConfirmation from "@/Shared/MyConfirmation.vue";

let props = defineProps({
    origin: String,
    club: Object,
    deletable: Boolean,
    displayStyles: Array,
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
    display: null,
    honor_years: '',
    use_items: false,
});

onMounted(() => {
    if (props.club !== undefined) {
        form.name = props.club.name;
        form.street = props.club.street;
        form.zipcode = props.club.zipcode;
        form.city = props.club.city;
        form.blsv_member = Boolean(props.club.blsv_member);
        form.bank = props.club.bank;
        form.account_owner = props.club.account_owner;
        form.iban = props.club.iban;
        form.bic = props.club.bic;
        form.sepa = props.club.sepa;
        form.sepa_date = props.club.sepa_date;
        form.display = props.club.display;
        form.logo = props.club.logo;
        form.honor_years = props.club.honor_years;
        form.use_items = Boolean(props.club.use_items)
    }
});

let submit = () => {
    if (editMode.value) {
        form.put(`/clubs/${props.club.id}`);
    } else {
        form.post('/clubs');
    }
};

let showDeletion = ref(false);
let deleteEntity = () => {
    showDeletion.value = false;
    Inertia.delete(`/clubs/${props.club.id}`);
};

const editMode = computed(() => props.club !== undefined);
const title = computed(() => editMode.value ? "Verein bearbeiten" : "Neuer Verein");
const submitButtonText = computed(() => editMode.value ? "Speichern" : "Hinzufügen");
const getLogoUrl = computed(() => form.logo ? '/storage/logo/' + form.logo : null);

function onLogoChanged(filename) {
    form.logo = filename;
}

function back() {
    window.history.back();
}

</script>

<template>
    <button
        tabindex="-1"
        class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
    ></button>
    <div class="relative z-30 w-full max-w-3xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
        <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
            <div class="font-medium text-lg text-gray-900 ml-3 mb-4">{{ title }}</div>

            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                <form  @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                    <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                        <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-8">
                            <MyImageUpload
                                label="Logo"
                                class="sm:col-span-2"
                                id="logo"
                                :image-url="getLogoUrl"
                                location="logo"
                                resize-height="150"
                                v-on:change="onLogoChanged"
                            />
                            <div class="sm:col-span-6 grid sm:grid-cols-6 gap-y-4">
                                <MyTextInput class="sm:col-span-6" v-model="form.name" :error="form.errors.name" id="name"
                                           label="Name" autofocus/>
                                <MyTextInput class="sm:col-span-6" v-model="form.street" :error="form.errors.street" id="street"
                                           label="Straße"/>
                                <MyTextInput class="sm:col-span-1" v-model="form.zipcode" :error="form.errors.zipcode"
                                    id="zipcode" label="PLZ" />
                                <MyTextInput class="sm:col-span-5 sm:ml-3" v-model="form.city" :error="form.errors.city"
                                    id="city" label="Ort" />
                                <MyTextInput class="sm:col-span-6" v-model="form.bank" :error="form.errors.bank"
                                    id="bank" label="Bankinstitut" />
                                <MyTextInput class="sm:col-span-6" v-model="form.account_owner" :error="form.errors.account_owner"
                                    id="account_owner" label="Kontoinhaber" />
                                <MyTextInput class="sm:col-span-6" v-model="form.iban" :error="form.errors.iban"
                                    id="iban" label="IBAN" />
                                <MyTextInput class="sm:col-span-6" v-model="form.bic" :error="form.errors.bic"
                                    id="bic" label="BIC" />
                                <MyTextInput class="sm:col-span-4" v-model="form.sepa" :error="form.errors.sepa"
                                    id="sepa" label="Sepa-Mandat" />
                                <MyTextInput class="sm:col-span-2 sm:ml-3" v-model="form.sepa_date" :error="form.errors.sepa_date"
                                           id="sepa_date"
                                           type='date' label="Sepa-Mandatsdatum" />
                                <MyTextInput class="sm:col-span-6" v-model="form.honor_years" :error="form.errors.honor_years"
                                           id="honor-years" label="Ehrungen Mitgliedsjahre" placeholder="10,20,..." />
                                <MyListbox class="sm:col-span-6" v-model="form.display" :error="form.errors.display"
                                          :options="props.displayStyles" id="display" label="Anzeige"/>
                                <MyCheckBox class="sm:col-span-6" v-model="form.blsv_member" :error="form.errors.blsv_member"
                                          id="blsv-member" label="BLSV-Mitglied"/>
                                <MyCheckBox class="sm:col-span-6" v-model="form.use_items" :error="form.errors.use_items"
                                          id="use-items" label="Inventar benutzen"/>
                            </div>
                        </div>
                        <div class="py-5">
                            <div class="flex justify-between">
                                <MyButton v-if="deletable" theme="danger" @click="showDeletion = true">Löschen</MyButton>
                                <div class="w-full flex justify-end">

                                    <MyButton v-if="origin" theme="abort" @click="Inertia.get(origin)">Abbrechen</MyButton>
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
    <MyConfirmation v-if="showDeletion" @canceled="showRestoreConfirmation = false" @confirmed="deleteEntity">
        {{ `Verein '${club.name}' löschen`}}
    </MyConfirmation>
</template>
