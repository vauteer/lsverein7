<script setup>
import {computed, ref, watch} from "vue";
import {Head, Link} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {PencilIcon, ChevronDoubleRightIcon, UsersIcon } from '@heroicons/vue/24/outline';
import {throttle} from "lodash";
import MyTextInput from "@/Shared/MyTextInput.vue";
import MyButton from "@/Shared/MyButton.vue";
import MyPagination from "@/Shared/MyPagination.vue";

let props = defineProps({
    subscriptions: Object,
    options: Object,
    canCreate: Boolean,
    sepaDate: String,
});

let showMembers = (id) => {
    Inertia.get('/members', {
            filter: `hasSubscription_${id}`,
        },
        {
            preserveState: true,
            replace: true,
        });
};

let debit = () => {
    Inertia.post('/subscriptions/debit', {
        subscriptions: selected.value,
        date: date.value,
    });
}

let search = ref(props.options.search);
const selected = ref([]);
const date = ref(props.sepaDate);
const partialSelection = computed(() => selected.value.length > 0 && selected.value.length < props.subscriptions.data.length)
const anySelected = computed(() => selected.value.length > 0);

watch(search, throttle(function (value) {
    Inertia.get('/subscriptions', {search: value}, {
        preserveState: true,
        replace: true,
    });
}, 300));

</script>

<template>
    <Head title="Beiträge"/>
    <div
        class="w-full max-w-2xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-4 px-4 sm:px-6 lg:px-8">
        <div class="flex mt-2">
            <input v-if="!anySelected" type="text" placeholder="Suchen..." v-model="search"
                   class="text-gray-700 px-2 my-2 text-base border rounded-lg"
            />
            <div v-else class="flex">
                <MyButton v-if="anySelected" theme="danger" @click="debit" class="my-2 ml-2">Abbuchen zum:</MyButton>
                <MyTextInput v-if="anySelected" class="my-2 ml-2" v-model="date" id="date" type="date" label=""/>
            </div>
        </div>

        <div class="mt-4 mb-4 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="relative w-12 px-6 sm:w-16 sm:px-8">
                                    <input type="checkbox" class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 sm:left-6" :checked="partialSelection || selected.length === props.subscriptions.data.length" :indeterminate="partialSelection" @change="selected = $event.target.checked ? subscriptions.data.map((s) => s.id) : []" />
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                    Name
                                </th>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                    Details
                                </th>
                                <th scope="col" class="px-3 py-3.5 w-6"><span class="sr-only">Status</span></th>
                                <th scope="col" class="px-3 py-3.5 w-6"><span class="sr-only">Show Members</span></th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-6">
                                    <span class="sr-only">Edit</span>
                                    <MyButton v-if="props.canCreate" @click="Inertia.get('/subscriptions/create')">Neu</MyButton>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="subscription in subscriptions.data" :key="subscription.id" class="text-gray-500">
                                <td class="relative w-12 px-6 sm:w-16 sm:px-8">
                                    <div v-if="selected.includes(subscription.id)" class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600"></div>
                                    <input type="checkbox" class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 sm:left-6" :value="subscription.id" v-model="selected" />
                                </td>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-bold">{{ subscription.name }}</div>
                                    <div>{{ subscription.amount }}</div>
                                </td>
                                <td class="px-3">
                                    <div>{{ subscription.transfer_text }}</div>
                                    <div>{{ subscription.memo }}</div>
                                </td>
                                <td class="px-3">

                                </td>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-blue-500 sm:pl-6">
                                    <a @click="showMembers(subscription.id)" as="button" class="cursor-pointer">
                                        Wer ?
                                    </a>
                                </td>
                                <td class="px-3">
                                    <div class="h-5">
                                        <Link v-if="subscription.modifiable" :href="`/subscriptions/${subscription.id}/edit`">
                                            <PencilIcon class="h-5 w-5 text-blue-500"/>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-if="subscriptions.data.length === 0"
                             class="text-gray-600 text-sm font-semibold ml-2"
                        >
                            Keine Daten
                        </div>
                        <MyPagination v-if="subscriptions.meta.last_page > 1" class="mt-6"
                                    :meta="subscriptions.meta"></MyPagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
