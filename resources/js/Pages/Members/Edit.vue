<script setup>
import {computed, ref, onMounted} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {useForm, Link, Head, usePage} from "@inertiajs/inertia-vue3";
import {PencilIcon} from '@heroicons/vue/24/outline';
import MyTextInput from "@/Shared/MyTextInput.vue";
import MyTextArea from "@/Shared/MyTextArea.vue"
import MyListbox from "@/Shared/MyListbox.vue";
import MyButton from "@/Shared/MyButton.vue";

let props = defineProps({
    isMember: { type: Boolean, default: false },
    date: String,
    origin: String,
    member: Object,
    genders: Array,
    paymentMethods: Array,
    sections: Array,
    subscriptions: Array,
    memberClubs: Object,
    memberSections: Object,
    memberSubscriptions: Object,
    memberEvents: Object,
    memberRoles: Object,
    memberItems: Object,
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
            form.memo = props.member.memo
    }
});

let submit = () => {
    if (editMode.value) {
        form.put(`/members/${props.member.id}`);
    } else {
        form.post('/members');
    }
};

let resign = () => {
    Inertia.put(`/members/${props.member.id}/resign`, { date: resignDate.value })
};

let resignDate = ref(props.date);
const club = computed(() => usePage().props.value.auth.club);
const editMode = computed(() => props.member !== undefined);
const title = computed(() => editMode.value ? "Mitglied bearbeiten" : "Neues Mitglied");
const submitButtonText = computed(() => editMode.value ? "Speichern" : "Hinzufügen");

</script>

<template>
    <button
        tabindex="-1"
        class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
    ></button>
    <div
        class="relative z-30 w-full mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2"
        :class="[editMode ? 'max-w-4xl' : 'max-w-xl']"
    >
        <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
            <div class="font-medium text-lg text-gray-900 ml-3 mb-4">{{ title }}</div>
            <div
                class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                    <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                        <div class="grid grid-cols-1 gap-y-4 gap-x-4"
                             :class="[editMode ? 'sm:grid-cols-6' : 'sm:grid-cols-3']">
                            <div class="sm:col-span-3 grid gap-y-4 gap-x-4">
                                <MyListbox class="sm:col-span-1" v-model="form.gender" :error="form.errors.gender"
                                          :options="props.genders" id="gender" label="Geschlecht" autofocus/>
                                <MyTextInput class="sm:col-span-3" v-model="form.first_name"
                                           :error="form.errors.first_name"
                                           id="first_name" label="Vorname"/>
                                <MyTextInput class="sm:col-span-3" v-model="form.surname" :error="form.errors.surname"
                                           id="surname"
                                           label="Nachname"/>
                                <MyTextInput class="sm:col-span-3" v-model="form.street" :error="form.errors.street"
                                           id="street" label="Straße"/>
                                <MyTextInput class="sm:col-span-1" v-model="form.zipcode" :error="form.errors.zipcode"
                                           id="zipcode" label="PLZ"/>
                                <MyTextInput class="sm:col-span-2" v-model="form.city" :error="form.errors.city"
                                           id="city" label="Wohnort"/>
                                <MyTextInput class="sm:col-span-1" v-model="form.birthday"
                                           :error="form.errors.birthday"
                                           id="birthday" type="date" label="Geburtstag"/>
                                <MyTextInput v-if="editMode" class="sm:col-span-1" v-model="form.death_day"
                                           :error="form.errors.death_day"
                                           id="death-day" type="date" label="Gestorben"/>
                                <MyTextInput class="sm:col-span-3" v-model="form.email" :error="form.errors.email"
                                           id="email" label="Email" type="email"/>
                                <MyTextInput class="sm:col-span-3" v-model="form.phone" :error="form.errors.phone"
                                           id="phone" label="Telefon" type="tel"/>
                                <MyTextInput v-if="!editMode" class="sm:col-span-1" v-model="form.entry_date"
                                           :error="form.errors.entry_date"
                                           id="entry-date" type="date" label="Eintritt"/>
                                <MyListbox v-if="!editMode" class="sm:col-span-2" v-model="form.section"
                                          :error="form.errors.section"
                                          :options="props.sections" id="section" label="Abteilung"/>
                                <MyListbox v-if="!editMode" class="sm:col-span-2" v-model="form.subscription"
                                          :error="form.errors.subscription"
                                          :options="props.subscriptions" id="subscription" label="Beitrag"
                                          nullValue="( Ohne )"/>
                                <MyListbox class="sm:col-span-1" v-model="form.payment_method"
                                          :error="form.errors.payment_method"
                                          :options="props.paymentMethods" id="payment-method" label="Zahlweise"/>
                                <MyTextInput class="sm:col-span-3" v-model="form.bank" :error="form.errors.bank"
                                           id="bank" label="Bank"/>
                                <MyTextInput class="sm:col-span-3" v-model="form.account_owner"
                                           :error="form.errors.account_owner"
                                           id="account_owner" label="Kontoinhaber"/>
                                <MyTextInput class="sm:col-span-3" v-model="form.iban" :error="form.errors.iban"
                                           id="iban" label="IBAN"/>
                                <MyTextInput class="sm:col-span-3" v-model="form.bic" :error="form.errors.bic"
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
                                                                Mitgliedschaft(en)
                                                            </th>
                                                            <th scope="col" class="relative w-5 py-0 pl-3 pr-1 sm:pr-2">
                                                                <MyButton class="py-1" @click="Inertia.get(`/members/${props.member.id}/club/create`)">
                                                                    Neu
                                                                </MyButton>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                        <tr v-for="membership in memberClubs.data">
                                                            <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                {{ membership.range }}
                                                            </td>
                                                            <td class="px-3">
                                                                <div class="flex justify-end">
                                                                    <Link v-if="membership.modifiable"
                                                                          :href="`/members/${props.member.id}/club/${membership.id}/edit`">
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
                                <div class="flex justify-end mt-2" v-if="isMember">
                                    <MyTextInput class="" v-model="resignDate" id="resign-date" type="date"/>
                                    <MyButton theme="danger" @click="resign" class="mx-2">Austritt</MyButton>
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
                                                            <th scope="col" class="relative w-5 py-0 pl-3 pr-1 sm:pr-2">
                                                                <MyButton class="py-1" @click="Inertia.get(`/members/${props.member.id}/section/create`)">
                                                                    Neu
                                                                </MyButton>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                        <tr v-for="memberSection in memberSections.data">
                                                            <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                {{ memberSection.name }}
                                                            </td>
                                                            <td class="px-3 py-2 text-sm text-gray-500">
                                                                {{ memberSection.range }}
                                                            </td>
                                                            <td class="px-3">
                                                                <div class="flex justify-end">
                                                                    <Link v-if="memberSection.modifiable"
                                                                          :href="`/members/${props.member.id}/section/${memberSection.id}/edit`">
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
                                                                Beiträge
                                                            </th>
                                                            <th scope="col">

                                                            </th>
                                                            <th scope="col" class="relative w-5 py-0 pl-3 pr-1 sm:pr-2">
                                                                <MyButton class="py-1" @click="Inertia.get(`/members/${props.member.id}/subscription/create`)">
                                                                    Neu
                                                                </MyButton>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                        <tr v-for="subscription in memberSubscriptions.data">
                                                            <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                {{ subscription.name }}
                                                            </td>
                                                            <td class="px-3 py-2 text-sm text-gray-500">
                                                                {{ subscription.memo }}
                                                            </td>
                                                            <td class="px-3">
                                                                <div class="flex justify-end">
                                                                    <Link v-if="subscription.modifiable"
                                                                          :href="`/members/${props.member.id}/subscription/${subscription.id}/edit`">
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
                                                                Ereignisse
                                                            </th>
                                                            <th scope="col">

                                                            </th>
                                                            <th scope="col" class="relative w-5 py-0 pl-3 pr-1 sm:pr-2">
                                                                <MyButton class="py-1" @click="Inertia.get(`/members/${props.member.id}/event/create`)">
                                                                    Neu
                                                                </MyButton>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                        <tr v-for="event in memberEvents.data">
                                                            <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                {{ event.name }}
                                                            </td>
                                                            <td class="px-3 py-2 text-sm text-gray-500">
                                                                {{ event.date }}
                                                            </td>
                                                            <td class="px-3">
                                                                <div class="flex justify-end">
                                                                    <Link v-if="event.modifiable"
                                                                          :href="`/members/${props.member.id}/event/${event.id}/edit`">
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
                                                                Funktionen
                                                            </th>
                                                            <th scope="col">

                                                            </th>
                                                            <th scope="col" class="w-5 py-0 pl-3 pr-1 sm:pr-2">
                                                                <MyButton class="py-1"
                                                                    @click="Inertia.get(`/members/${props.member.id}/role/create`)"
                                                                >
                                                                    Neu
                                                                </MyButton>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                        <tr v-for="role in memberRoles.data">
                                                            <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                {{ role.name }}
                                                            </td>
                                                            <td class="px-3 py-2 text-sm text-gray-500">
                                                                {{ role.range }}
                                                            </td>
                                                            <td class="px-3">
                                                                <div class="flex justify-end">
                                                                    <Link v-if="role.modifiable"
                                                                          :href="`/members/${props.member.id}/role/${role.id}/edit`">
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
                                <div v-if="club.useItems" class="px-4 sm:px-6 lg:px-8 mt-3">
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
                                                                Inventar
                                                            </th>
                                                            <th scope="col">

                                                            </th>
                                                            <th scope="col"
                                                                class="relative w-5 py-0 pl-3 pr-1 sm:pr-2">
                                                                <MyButton class="py-1" @click="Inertia.get(`/members/${props.member.id}/item/create`)">
                                                                    Neu
                                                                </MyButton>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                        <tr v-for="item in memberItems.data">
                                                            <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                {{ item.name }}
                                                            </td>
                                                            <td class="px-3 py-2 text-sm text-gray-500">
                                                                {{ item.range }}
                                                            </td>
                                                            <td class="px-3">
                                                                <div class="flex justify-end">
                                                                    <Link v-if="item.modifiable"
                                                                          :href="`/members/${props.member.id}/item/${item.id}/edit`">
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
</template>
