<script setup>
import {computed, ref, reactive, watch} from "vue";
import {Head, Link} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {PencilIcon, IdentificationIcon, CloudIcon } from '@heroicons/vue/24/outline';
import {throttle} from "lodash";
import MyLayout from "@/Shared/MyLayout.vue";
import MyPagination from "@/Shared/MyPagination.vue";
import MyButton from "@/Shared/MyButton.vue";
import MyTextInput from "@/Shared/MyTextInput.vue";
import MySelect from "@/Shared/MySelect.vue";

let props = defineProps({
    members: Object,
    options: Object,
    filters: Object,
    clubAdmin: Boolean,
    currentFilter: String,
    years: Object,
    currentYear: Number,
    sorts: Object,
    currentSort: Number,
});

const outputUrl = computed(() => (format) => {
    let params = new URLSearchParams(state).toString().replace('null', '');
    return `/members/${format}?${params}`;
});

const state = reactive({
    search: props.options.search,
    filter: String(props.currentFilter),
    year: String(props.currentYear),
    sort: String(props.currentSort),
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

watch(state, throttle(function (newValue) {
    refresh();
}, 300));

</script>

<template>
    <MyLayout>
        <Head title="Mitglieder"/>
        <div
            class="w-full max-w-4xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-8 py-2 mt-2">
                <MySelect class="sm:col-span-3" v-model="state.filter" :options="props.filters" id="quick-filters" :label="`Auswahl (${members.meta.total} Personen)`" />
                <MyTextInput class="sm:col-span-2" v-model="state.search" id="search" label="Suchen"
                       placeholder="Suchen..."/>
                <MySelect class="sm:col-span-2" v-model="state.sort" :options="props.sorts" id="sorts" label="Sortierung"/>
                <MySelect class="sm:col-span-1" :disabled="!yearEnabled" v-model="state.year" :options="props.years" id="years" label="Jahr" :hideDisabled="true"/>
            </div>

            <div class="flex">
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
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 hidden md:table-cell">
                                        Details
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 w-6">
                                        <div v-if="members.data.length > 0">
                                            <a :href="outputUrl('pdf')" target="_blank">PDF</a>
                                        </div>
                                        <div v-if="members.data.length > 0">
                                            <a :href="outputUrl('csv')" target="_blank">CSV</a>
                                        </div>
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
    </MyLayout>
</template>
