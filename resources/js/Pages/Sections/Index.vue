<script setup>
import {computed, ref, watch} from "vue";
import {Head, Link} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {PencilIcon, LockClosedIcon} from '@heroicons/vue/outline';
import {throttle} from "lodash";
import Category from "@/Shared/Category.vue";
import Pagination from "@/Shared/Pagination.vue";
import Layout from "@/Shared/Layout.vue";

let props = defineProps({
    sections: Object,
    filters: Object,
    canCreate: Boolean,
});

const createUrl = computed(() => {
    return props.canCreate ? "/sections/create" : "";
});

let search = ref(props.filters.search);

watch(search, throttle(function (value) {
    Inertia.get('/sections', {search: value}, {
        preserveState: true,
        replace: true,
    });
}, 300));

</script>

<template>
    <Layout>
        <Head title="Abteilungen"/>

        <div
            class="w-full max-w-2xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
            <Category :createUrl="createUrl" v-model="search">Abteilungen</Category>

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
                                    <th scope="col" class="px-3 py-3.5 w-6"><span class="sr-only">Status</span></th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-6">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="section in sections.data" :key="section.id" class="text-gray-500">
                                    <td class="py-3 pl-2 pr-3 text-right sm:pl-6">
                                        {{ section.id }}
                                    </td>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                        <div class="font-bold">{{ section.name }}</div>
                                    </td>
                                    <td class="px-3">
                                    </td>
                                    <td class="px-3">
                                        <div class="h-5">
                                            <Link v-if="section.modifiable" :href="`/sections/${section.id}/edit`">
                                                <PencilIcon class="h-5 w-5 text-blue-500"/>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div v-if="sections.data.length === 0"
                                 class="text-gray-600 text-sm font-semibold ml-2"
                            >
                                Keine Daten
                            </div>
                            <Pagination v-if="sections.meta.last_page > 1" class="mt-6"
                                        :meta="sections.meta"></Pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
