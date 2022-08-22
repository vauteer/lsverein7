<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm, Link, Head} from "@inertiajs/inertia-vue3";
import {PencilIcon, LockClosedIcon} from '@heroicons/vue/outline';
import Layout from "@/Shared/Layout.vue";
import TextInput from "@/Shared/TextInput.vue";
import MyTextArea from "@/Shared/MyTextArea.vue"
import MySelect from "@/Shared/MySelect.vue";
import AbortButton from "@/Shared/AbortButton.vue";
import SubmitButton from "@/Shared/SubmitButton.vue";
import DeleteButton from "@/Shared/DeleteButton.vue";

let props = defineProps({
    member: Object,
    genders: Object,
    paymentMethods: Object,
    sections: Object,
    subscriptions: Object,
    memberships: Object,
    memberSections: Object,
    memberSubscriptions: Object,
    memberEvents: Object,
    memberRoles: Object,
});

let form = useForm({
    surname: '',
    first_name: '',
    gender: 'm',
    birthday: '',
    death_day: '',
    street: '',
    zipcode: '',
    city: '',
    email: null,
    phone: null,
    payment_method: 'k',
    bank: null,
    account_owner: null,
    iban: null,
    bic: null,
    memo: null,
    entry_date: null,
    section: null,
    subscription: null,
});

onMounted(() => {
    if (props.member !== undefined) {
        form.surname = props.member.surname,
            form.first_name = props.member.first_name,
            form.gender = props.member.gender,
            form.birthday = props.member.birthday,
            form.death_day = props.member.death_day,
            form.street = props.member.street,
            form.zipcode = props.member.zipcode,
            form.city = props.member.city,
            form.email = props.member.email,
            form.phone = props.member.phone,
            form.payment_method = props.member.payment_method,
            form.bank = props.member.bank,
            form.account_owner = props.member.account_owner,
            form.iban = props.member.iban,
            form.bic = props.member.bic,
            form.memo = props.member.memo,

            editMode.value = true;
    }
    document.getElementById('gender').focus();
});

let submit = () => {
    if (editMode.value === true) {
        form.put(`/members/${props.member.id}`);
    } else {
        form.post('/members');
    }
};

let deleteEntity = () => {
    if (confirm('Mitglied löschen ?')) {
        Inertia.delete(`/members/${props.member.id}`);
    }
};

let editMode = ref(false);

const getTitle = computed(() => {
    return editMode.value ? "Mitglied bearbeiten" : "Neues Mitglied";
});

const getSubmitButtonText = computed(() => {
    return editMode.value ? "Speichern" : "Hinzufügen";
});

</script>

<template>
    <Head title="Mitglieder"/>

    <Layout>
        <button
            tabindex="-1"
            class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
        ></button>
        <div
            class="relative z-30 w-full mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2"
            :class="[editMode ? 'max-w-4xl' : 'max-w-xl']"
        >
            <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
                <div class="font-medium text-lg text-gray-900 ml-3 mb-4">{{ getTitle }}</div>
                <div
                    class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                    <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                        <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                            <div class="grid grid-cols-1 gap-y-4 gap-x-4"
                                 :class="[editMode ? 'sm:grid-cols-6' : 'sm:grid-cols-3']">
                                <div class="sm:col-span-3 grid gap-y-4 gap-x-4">
                                    <MySelect class="sm:col-span-1" v-model="form.gender" :error="form.errors.gender"
                                              :options="props.genders" id="gender" label="Geschlecht"/>
                                    <TextInput class="sm:col-span-3" v-model="form.first_name"
                                               :error="form.errors.first_name"
                                               id="first_name" label="Vorname"/>
                                    <TextInput class="sm:col-span-3" v-model="form.surname" :error="form.errors.surname"
                                               id="surname"
                                               label="Nachname"/>
                                    <TextInput class="sm:col-span-3" v-model="form.street" :error="form.errors.street"
                                               id="street" label="Straße"/>
                                    <TextInput class="sm:col-span-1" v-model="form.zipcode" :error="form.errors.zipcode"
                                               id="zipcode" label="PLZ"/>
                                    <TextInput class="sm:col-span-2" v-model="form.city" :error="form.errors.city"
                                               id="city" label="Wohnort"/>
                                    <TextInput class="sm:col-span-1" v-model="form.birthday"
                                               :error="form.errors.birthday"
                                               id="birthday" type="date" label="Geburtstag"/>
                                    <TextInput v-if="editMode" class="sm:col-span-1" v-model="form.death_day"
                                               :error="form.errors.death_day"
                                               id="death-day" type="date" label="Gestorben"/>
                                    <TextInput class="sm:col-span-3" v-model="form.email" :error="form.errors.email"
                                               id="email" label="Email" type="email"/>
                                    <TextInput class="sm:col-span-3" v-model="form.phone" :error="form.errors.phone"
                                               id="phone" label="Telefon" type="tel"/>
                                    <TextInput v-if="!editMode" class="sm:col-span-1" v-model="form.entry_date"
                                               :error="form.errors.entry_date"
                                               id="entry-date" type="date" label="Eintritt"/>
                                    <MySelect v-if="!editMode" class="sm:col-span-2" v-model="form.section"
                                              :error="form.errors.section"
                                              :options="props.sections" id="section" label="Abteilung"/>
                                    <MySelect v-if="!editMode" class="sm:col-span-2" v-model="form.subscription"
                                              :error="form.errors.subscription"
                                              :options="props.subscriptions" id="subscription" label="Beitrag"
                                              null-value="( Ohne )"/>
                                    <MySelect class="sm:col-span-1" v-model="form.payment_method"
                                              :error="form.errors.payment_method"
                                              :options="props.paymentMethods" id="payment-method" label="Zahlweise"/>
                                    <TextInput class="sm:col-span-3" v-model="form.bank" :error="form.errors.bank"
                                               id="bank" label="Bank"/>
                                    <TextInput class="sm:col-span-3" v-model="form.account_owner"
                                               :error="form.errors.account_owner"
                                               id="account_owner" label="Kontoinhaber"/>
                                    <TextInput class="sm:col-span-3" v-model="form.iban" :error="form.errors.iban"
                                               id="iban" label="IBAN"/>
                                    <TextInput class="sm:col-span-3" v-model="form.bic" :error="form.errors.bic"
                                               id="bic" label="BIC"/>
                                    <MyTextArea class="sm:col-span-3" v-model="form.memo" :error="form.errors.memo"
                                                id="memo" label="Memo"/>
                                </div>
                                <div v-if="editMode" class="sm:col-span-3">
                                    <div class="px-4 sm:px-6 lg:px-8">
                                        <div class="flex flex-col">
                                            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                <div class="inline-block min-w-full py-2 align-middle md:px-2">
                                                    <div
                                                        class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                                        <table class="min-w-full divide-y divide-gray-300">
                                                            <thead class="bg-gray-50">
                                                            <tr>
                                                                <th scope="col"
                                                                    class="py-2 pl-4 pr-3 text-left text-base font-semibold text-gray-900">
                                                                    Mitgliedschaften
                                                                </th>
                                                                <th scope="col"
                                                                    class="relative w-5 py-0 pl-3 pr-1 sm:pr-2">
                                                                    <Link
                                                                        class="rounded-md border border-transparent bg-indigo-600 px-4 py-1 my-1 text-sm font-medium text-white shadow-sm enabled:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                                                                        :href="`/memberships/${props.member.id}/create`" as="button" type="button">
                                                                        Neu
                                                                    </Link>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                            <tr v-for="membership in memberships.data">
                                                                <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                    {{ membership.range }}
                                                                </td>
                                                                <td class="px-3">
                                                                    <div class="flex justify-end">
                                                                        <Link v-if="membership.modifiable"
                                                                              :href="`/memberships/${membership.id}/edit`">
                                                                            <PencilIcon class="h-5 w-5 text-blue-500"/>
                                                                        </Link>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 sm:px-6 lg:px-8 mt-3">
                                        <div class="flex flex-col">
                                            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                <div class="inline-block min-w-full py-2 align-middle md:px-2">
                                                    <div
                                                        class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                                        <table class="min-w-full divide-y divide-gray-300">
                                                            <thead class="bg-gray-50">
                                                            <tr>
                                                                <th scope="col"
                                                                    class="py-2 pl-4 pr-3 text-left text-base font-semibold text-gray-900">
                                                                    Sparten
                                                                </th>
                                                                <th scope="col">

                                                                </th>
                                                                <th scope="col"
                                                                    class="relative w-5 py-0 pl-3 pr-1 sm:pr-2">
                                                                    <Link
                                                                        class="rounded-md border border-transparent bg-indigo-600 px-4 py-1 my-1 text-sm font-medium text-white shadow-sm enabled:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                                                                        :href="`/members/${props.member.id}/section/create`" as="button" type="button">
                                                                        Neu
                                                                    </Link>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                            <tr v-for="membership in memberSections.data">
                                                                <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                    {{ membership.name }}
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500">
                                                                    {{ membership.range }}
                                                                </td>
                                                                <td class="px-3">
                                                                    <div class="flex justify-end">
                                                                        <Link v-if="membership.modifiable"
                                                                              :href="`/members/section/${membership.id}/edit`">
                                                                            <PencilIcon class="h-5 w-5 text-blue-500"/>
                                                                        </Link>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-5">
                                <div class="flex justify-between">
                                    <DeleteButton v-if="editMode" :onDelete="deleteEntity"/>
                                    <div class="w-full flex justify-end">
                                        <AbortButton href="/members"/>
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
