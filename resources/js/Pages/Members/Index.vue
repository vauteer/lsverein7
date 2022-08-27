<script setup>
import {computed, ref, watch} from "vue";
import {Head, Link} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {PencilIcon, LockClosedIcon, CloudIcon } from '@heroicons/vue/outline';
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import {throttle} from "lodash";
import Layout from "@/Shared/Layout.vue";
import Category from "@/Shared/Category.vue";
import Pagination from "@/Shared/Pagination.vue";
import MySelect from "@/Shared/MySelect.vue";

let props = defineProps({
    members: Object,
    memberCount: Number,
    filters: Object,
    searchString: String,
    canCreate: Boolean,
    currentFilter: String,
    years: Object,
    currentYear: Number,
    sorts: Object,
    currentSort: Number,
});

const createUrl = computed(() => {
    return props.canCreate ? "/members/create" : "";
});

let search = ref(props.searchString);
let filter = ref(String(props.currentFilter));
let year = ref(String(props.currentYear));
let sort = ref(String(props.currentSort));

let refresh = () => {
    Inertia.get('/members', {
        filter: filter.value ,
        search: search.value,
        year: year.value,
        sort: sort.value,
    },
        {
        preserveState: true,
        replace: true,
    });
};

let yearEnabled = computed(() => {
    return !props.currentFilter.startsWith('subscription_')
});

watch(search, throttle(function (value) {
    refresh();
}, 300));

watch(filter, (newValue) => {
    search.value = '';
    refresh();
});

watch(year, () => {
    search.value = '';
    refresh();
});

watch(sort, () => {
    search.value = '';
    refresh();
});

</script>

<template>
    <Layout>
        <Head title="Mitglieder"/>

        <div
            class="w-full max-w-3xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
            <Category :createUrl="createUrl" v-model="search">{{ memberCount }} Personen</Category>
            <div class="flex">
                <MySelect v-model="filter" :options="props.filters" id="quick-filters" label=""
                          class="w-60"
                />
                <MySelect v-if="yearEnabled" v-model="year" :options="props.years" id="years" label=""
                          class="w-40 ml-2"
                />
                <MySelect v-model="sort" :options="props.sorts" id="sorts" label=""
                          class="w-40 ml-2"
                />
            </div>
            <div class="mt-4 mb-4 flex flex-col">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="min-w-full py-2 align-middle md:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="w-5 py-3.5 pl-2 pr-3 sm:pl-6 text-right">
                                        Nr
                                    </th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Name
                                    </th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 hidden sm:table-cell">
                                        Details
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 w-6"><span class="sr-only">Status</span></th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-6">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="member in members.data" :key="member.id" class="text-gray-500">
                                    <td class="py-2 pl-2 pr-3 text-right sm:pl-6">
                                        {{ member.id }}
                                    </td>
                                    <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm sm:pl-6">
                                        <div class="font-bold" :class="member.isMember ? '' : 'line-through' ">
                                            {{ member.surname }} {{ member.first_name}} {{ member.roles }}
                                        </div>
                                        <div>{{ member.birthday }} {{ member.age }} {{ member.gender }} {{ member.membershipYears }}</div>
                                    </td>
                                    <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm sm:pl-6 hidden sm:table-cell">
                                        <div>{{ member.address }}</div>
                                        <div>{{ member.subscriptions }} {{ member.sections }} {{ member.lastEvent }}</div>
                                    </td>
                                    <td class="px-3">
                                        <div class="h-5">
                                                <CloudIcon v-if="member.gone" class="h-5 w-5 text-blue-500"/>
                                        </div>
                                    </td>
                                    <td class="px-3">
                                        <div class="h-5">
                                            <Link v-if="member.modifiable" :href="`/members/${member.id}/edit`">
                                                <PencilIcon class="h-5 w-5 text-blue-500"/>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div v-if="members.data.length === 0"
                                 class="text-gray-600 text-sm font-semibold ml-2"
                            >
                                Keine Daten
                            </div>
                            <Pagination v-if="members.meta.last_page > 1" class="mt-6"
                                        :meta="members.meta"></Pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
