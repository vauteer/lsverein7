<script setup>
import {Head, Link} from '@inertiajs/inertia-vue3';
import Layout from "@/Shared/Layout.vue";

let props = defineProps({
    title: String,
    downloads: Object,
    outStandings: Object,
});

</script>

<template>
    <Layout>
        <Head title="downloads"/>

        <div
            class="w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
            <div class="w-full text-2xl font-medium text-gray-900 mt-2">{{ title }}</div>

            <div class="overflow-hidden bg-white shadow sm:rounded-md my-4">
                <ul role="list" class="divide-y divide-gray-200 text-base">
                    <li v-for="download in downloads">
                        <a :href="download.href" download class="block hover:bg-gray-50">
                            <div class="flex items-center px-4 py-4 sm:px-6">
                                <div class="flex min-w-0 flex-1 items-center">
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <p class="truncate font-medium text-indigo-600">{{ download.name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="w-full text-2xl font-medium text-gray-900 mt-2">Außenstände</div>

            <div class="overflow-hidden bg-white shadow sm:rounded-md my-4">
                <ul role="list" class="divide-y divide-gray-200 text-sm">
                    <li v-for="outStanding in outStandings">
                        <div class="grid grid-cols-3 gap-4 gap-y-4 px-4 py-2 sm:px-6">
                            <div>{{ outStanding.name }}</div>
                            <div>{{ outStanding.paymentMethod }}</div>
                            <div>{{ outStanding.subscription }}</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </Layout>
</template>
