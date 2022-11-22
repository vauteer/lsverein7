<script setup>
import {computed, ref, reactive, watch} from "vue";
import {Head, Link} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import {PencilIcon, IdentificationIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';
import {throttle} from "lodash";
import MyPagination from "@/Shared/MyPagination.vue";
import MyButton from "@/Shared/MyButton.vue";
import MyTextInput from "@/Shared/MyTextInput.vue";
import MyListbox from "@/Shared/MyListbox.vue";
import MySelect from "@/Shared/MySelect.vue";

let props = defineProps({
    members: Object,
    options: Object,
    filters: Array,
    clubAdmin: Boolean,
    currentFilter: String,
    years: Array,
    currentYear: Number,
    sorts: Array,
    currentSort: Number,
    exportFormats: Object,
});

const outputUrl = computed(() => (format) => {
    let params = new URLSearchParams(state).toString().replace('null', '');
    return `/members/${format}?${params}`;
});

let exportFormat = ref('');

const state = reactive({
    search: props.options.search,
    filter: props.currentFilter,
    year: props.currentYear,
    sort: props.currentSort,
});

let refresh = () => {
    Inertia.get('/members', state,
        {
        preserveState: true,
        replace: true,
    });
};

let yearEnabled = computed(() => {
    return !state.filter.startsWith('subscription_')
});

watch(state, throttle(() => refresh(), 300));

watch(exportFormat, (newValue) => {
    console.log(newValue);
    if (newValue === "")
        return;

    const params = new URLSearchParams(state).toString().replace('null', '');
    const url = `/members/${newValue}?${params}`;
    document.getElementById('export-format').value = "";
    window.open(url, '_blank');
    // Inertia.get(url);
});

</script>

<template>
    <Head title="Mitglieder"/>
    <div
        class="w-full max-w-4xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-8 py-2 mt-2">
            <MySelect class="sm:col-span-3" v-model="state.filter" :options="props.filters" id="quick-filters" :label="`Auswahl (${members.meta.total} Personen)`" />
            <MyTextInput class="sm:col-span-2" v-model="state.search" id="search" label="Suchen"
                   placeholder="Suchen..."/>
            <MyListbox class="sm:col-span-2" v-model="state.sort" :options="props.sorts" id="sorts" label="Sortierung"/>
            <MyListbox class="sm:col-span-1" :disabled="!yearEnabled" v-model="state.year" :options="props.years" id="years" label="Jahr" :hideDisabled="true"/>
        </div>

        <div class="flex">
        </div>
        <div class="mt-4 mb-4 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr class="font-semibold text-gray-900">
                                <th scope="col" class="w-5 py-3.5 pl-2 pr-3 sm:pl-6 text-right">
                                    Nr
                                </th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left sm:pl-6">
                                    Name
                                </th>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left sm:pl-6 hidden md:table-cell">
                                    <Popover v-if="members.data.length > 0" class="relative" v-slot="{ open }">
                                        <PopoverButton :class="[open ? 'text-gray-900' : 'text-gray-500', 'group inline-flex items-center rounded-md bg-white text-base font-medium hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']">
                                            <span>Export</span>
                                            <ChevronDownIcon :class="[open ? 'text-gray-600' : 'text-gray-400', 'ml-2 h-5 w-5 group-hover:text-gray-500']" aria-hidden="true" />
                                        </PopoverButton>

                                        <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                                            <PopoverPanel class="absolute left-1/2 z-10 mt-3 w-screen max-w-xs -translate-x-1/2 transform px-2 sm:px-0">
                                                <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                                    <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                                                        <a  :href="outputUrl('pdf')" target="_blank"
                                                            class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50"
                                                        >
                                                            <p class="text-base font-medium text-gray-900"> PDF </p>
                                                            <p class="mt-1 text-sm text-gray-500"> Adobe PDF </p>
                                                        </a>
                                                        <a  :href="outputUrl('vcf')" target="_blank"
                                                            class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50"
                                                        >
                                                            <p class="text-base font-medium text-gray-900"> vCard </p>
                                                            <p class="mt-1 text-sm text-gray-500"> elektronische Visitenkarten </p>
                                                        </a>
                                                        <a  :href="outputUrl('csv')" target="_blank"
                                                            class="-m-3 block rounded-md p-3 transition duration-150 ease-in-out hover:bg-gray-50"
                                                        >
                                                            <p class="text-base font-medium text-gray-900"> CSV </p>
                                                            <p class="mt-1 text-sm text-gray-500"> Komma-separierte Datei </p>
                                                        </a>
                                                    </div>
                                                </div>
                                            </PopoverPanel>
                                        </transition>
                                    </Popover>
                                </th>
                                <th scope="col" class="px-3 py-3.5 w-6 text-center">
                                </th>
                                <th scope="col" class="relative pl-3 pr-2 sm:pr-2 w-6">
                                    <MyButton v-if="clubAdmin" @click="Inertia.get('/members/create')">Neu</MyButton>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="member in members.data" :key="member.id" class="text-gray-500">
                                <td class="py-2 pl-2 pr-3 text-right sm:pl-6">
                                    {{ member.id }}
                                </td>
                                <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-bold" :class="member.isMember ? '' : 'text-gray-400' ">
                                        {{ member.surname }} {{ member.first_name}} <span v-if="member.gone">†</span>
                                    </div>
                                    <div>{{ member.birthday }} {{ member.age }} <span v-if="clubAdmin">/ {{ member.membershipYears }}</span> Jahre</div>
                                </td>
                                <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm sm:pl-6 hidden md:table-cell">
                                    <div>{{ member.sections }} {{ member.roles }}</div>
                                    <div v-if="clubAdmin">{{ member.subscriptions }} {{ member.lastEvent }}</div>
                                </td>
                                <td class="px-3">
                                    <Link :href="`/members/${member.id}/show`">
                                        <IdentificationIcon class="h-5 w-5 text-blue-500" />
                                    </Link>
                                </td>
                                <td class="px-3">
                                        <Link v-if="member.modifiable" :href="`/members/${member.id}/edit`">
                                            <PencilIcon class="h-5 w-5 text-blue-500"/>
                                        </Link>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-if="members.data.length === 0"
                             class="text-gray-600 text-sm font-semibold ml-2"
                        >
                            Keine Daten
                        </div>
                        <MyPagination v-if="members.meta.last_page > 1" class="mt-6"
                                    :meta="members.meta"></MyPagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
