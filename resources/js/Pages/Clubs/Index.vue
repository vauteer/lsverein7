<script setup>
import {computed, ref, watch} from "vue";
import {Head, Link} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {PencilIcon, ChevronDoubleRightIcon, CheckIcon } from '@heroicons/vue/24/outline';
import {throttle} from "lodash";
import MyLayout from "@/Shared/MyLayout.vue";
import MyPagination from "@/Shared/MyPagination.vue";
import MyButton from "@/Shared/MyButton.vue";

let props = defineProps({
    auth: Object,
    clubs: Object,
    options: Object,
    canCreate: Boolean,
});

let search = ref(props.options.search);

watch(search, throttle(function (value) {
    Inertia.get('/clubs', {search: value}, {
        preserveState: true,
        replace: true,
    });
}, 300));

</script>

<template>
    <MyLayout>
        <Head title="Vereine"/>
        <div
            class="w-full max-w-2xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
            <input type="text" placeholder="Suchen..." v-model="search"
                   class="text-gray-700 px-2 mr-4 my-2 text-base border rounded-lg"
            />

            <div class="mt-4 mb-4 flex flex-col">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="min-w-full py-2 align-middle md:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-3.5 w-6"><span class="sr-only">Switch Club</span></th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Name
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Details
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 w-6"><span class="sr-only">Status</span></th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-6">
                                        <span class="sr-only">Edit</span>
                                        <MyButton v-if="props.canCreate" @click="Inertia.get('/clubs/create')">Neu</MyButton>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="club in clubs.data" :key="club.id" class="text-gray-500">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                        <Link v-if="club.id !== auth.club.id"
                                              :href="`/clubs/${club.id}/change`"
                                              method="post" as="button"
                                        >
                                            <ChevronDoubleRightIcon class="h-5 w-5 text-blue-500" />
                                        </Link>
                                        <CheckIcon v-else class="h-5 w-5"/>
                                    </td> <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                        <div class="font-bold">{{ club.name }}</div>
                                    </td>
                                    <td class="px-3">
                                        <div class="font-bold">{{ club.street }}</div>
                                        <div class="font-bold">{{ club.zipcode }} {{ club.city }}</div>
                                    </td>
                                    <td class="px-3">
                                        <Link :href="`/clubs/${club.id}/blsv-statistic`"
                                              method="get" as="button" class="text-blue-500"
                                        >
                                            BLSV-Statistik
                                        </Link>
                                    </td>
                                    <td class="px-3">
                                        <div class="h-5">
                                            <Link v-if="club.modifiable" :href="`/clubs/${club.id}/edit`">
                                                <PencilIcon class="h-5 w-5 text-blue-500"/>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div v-if="clubs.data.length === 0"
                                 class="text-gray-600 text-sm font-semibold ml-2"
                            >
                                Keine Daten
                            </div>
                            <MyPagination v-if="clubs.meta.last_page > 1" class="mt-6"
                                        :meta="clubs.meta"></MyPagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MyLayout>
</template>
