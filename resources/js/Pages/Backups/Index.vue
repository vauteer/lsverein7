<script setup>
import { computed, ref } from "vue";
import {Head, Link} from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import MyCategory from "@/Shared/MyCategory.vue";
import MyLayout from "@/Shared/MyLayout.vue";
import MyConfirmation from "@/Shared/MyConfirmation.vue";
import MyDeleteButton from "@/Shared/MyDeleteButton.vue";

let props = defineProps({
    backups: Object,
    isDirty: Boolean,
});

let showRestoreConfirmation = ref(false);
let currentBackup = ref(null);

let confirmRestore = (backup) => {
    currentBackup.value = backup;
    showRestoreConfirmation.value = true;
}

let restoreBackup = () => {
    showRestoreConfirmation.value = false;
    Inertia.post('/backups/restore', {
        filename: currentBackup.value.filename,
    });
};

let dsa = computed(() => {
    return !props.isDirty;
})

</script>

<template>
    <div>
        <Head title="Backups"/>

        <MyLayout>
            <div
                class="w-full max-w-2xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
                <MyCategory createUrl="/backups/create" :search="false"
                          button-title="Backup erstellen" :button-disabled="dsa">
                    Backups
                </MyCategory>

                <div class="mt-4 mb-4 flex flex-col">
                    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="min-w-full py-2 align-middle md:px-6 lg:px-8">
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                            Datum
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-6">
                                            <span class="sr-only">Backup</span>
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-6">
                                            <span class="sr-only">Restore</span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="(backup, index) in backups.data" :key="index" class="text-gray-500">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                            <div class="font-bold">{{ backup.date }}</div>
                                        </td>
                                        <td class="px-3">
                                            <a class="text-blue-500"
                                               :href="route('backups.download', backup.filename)">
                                                Download
                                            </a>
                                        </td>
                                        <td class="px-3">
                                            <MyDeleteButton @click.prevent="confirmRestore(backup)">
                                                Wiederherstellen
                                            </MyDeleteButton>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div v-if="backups.data.length === 0"
                                     class="text-gray-600 text-sm font-semibold ml-2"
                                >
                                    Keine Daten
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <MyConfirmation v-if="showRestoreConfirmation" @canceled="showRestoreConfirmation = false" @confirmed="restoreBackup" subText="Die jetzigen Daten werden vorher gesichert">
                {{ `${currentBackup.date} wiederherstellen ?` }}
            </MyConfirmation>
        </MyLayout>
    </div>
</template>
