<script setup>
import {computed, ref, watch} from "vue";
import {Head, Link} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {PencilIcon, GlobeAltIcon } from '@heroicons/vue/24/outline';
import {throttle} from "lodash";
import MyLayout from "@/Shared/MyLayout.vue";
import MyTextInput from "@/Shared/MyTextInput.vue";
import MyActionLink from "@/Shared/MyActionLink.vue";
import MyPagination from "@/Shared/MyPagination.vue";

let props = defineProps({
    debits: Object,
    filters: Object,
    canCreate: Boolean,
    sepaDate: String,
});

let showMembers = (id) => {
    Inertia.get('/members', {
            filter: `hadDebit_${id}`,
        },
        {
            preserveState: true,
            replace: true,
        });
};

let search = ref(props.filters.search);
const date = ref(props.sepaDate);

watch(search, throttle(function (value) {
    Inertia.get('/debits', {search: value}, {
        preserveState: true,
        replace: true,
    });
}, 300));

</script>

<template>
    <MyLayout>
        <Head title="Lastschriften"/>

        <div
            class="w-full max-w-2xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
            <div class="w-full flex justify-between">
                <input type="text" placeholder="Suchen..." v-model="search"
                       class="text-gray-700 px-2 mr-4 my-2 text-base border rounded-lg"
                />
                <div v-if="debits.data.length > 0" class="flex">
                    <MyActionLink class="my-2 ml-2" href="/debits/debit" method="post"
                                  :data="{ date: date }"
                    >Abbuchen zum:</MyActionLink>
                    <MyTextInput class="my-2 ml-2" v-model="date" id="date" type="date" label=""/>
                </div>
            </div>

            <div class="mt-4 mb-4 flex flex-col">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="min-w-full py-2 align-middle md:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Name
                                    </th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Details
                                    </th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-6">
                                        <span class="sr-only">Edit</span>
                                        <MyActionLink v-if="props.canCreate" href="/debits/create">Neu</MyActionLink>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="debit in debits.data" :key="debit.id" class="text-gray-500">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                        <div class="font-bold">{{ debit.member_name }}</div>
                                        <div class="">{{ debit.transfer_text }}</div>
                                    </td>
                                    <td class="px-3">
                                        <div class="font-bold">{{ debit.amount }}</div>
                                        <div class="">Fällig: {{ debit.due_at }}</div>
                                    </td>
                                    <td class="px-3">
                                        <div class="h-5">
                                            <Link v-if="debit.modifiable" :href="`/debits/${debit.id}/edit`">
                                                <PencilIcon class="h-5 w-5 text-blue-500"/>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div v-if="debits.data.length === 0"
                                 class="text-gray-600 text-sm font-semibold ml-2"
                            >
                                Keine Daten
                            </div>
                            <MyPagination v-if="debits.meta.last_page > 1" class="mt-6"
                                          :meta="debits.meta"></MyPagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MyLayout>
</template>
