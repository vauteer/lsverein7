<script setup>
import MyButton from "@/Shared/MyButton.vue";

let props = defineProps({
    origin: String,
    downloads: Object,
});

</script>

<template>
    <button
        tabindex="-1"
        class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
    ></button>
    <div
        class="relative z-30 w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2 px-4 sm:px-6 lg:px-8">
        <div class="w-full text-2xl font-medium text-gray-900 mt-2">Downloads für BLSV-Statistik</div>

        <div class="overflow-hidden bg-white shadow sm:rounded-md my-4">
            <ul role="list" class="divide-y divide-gray-200 text-base">
                <li v-for="download in downloads">
                    <a :href="download.href" class="block hover:bg-gray-50">
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
        <div class="py-5 flex justify-end">
            <MyButton theme="abort" @click="Inertia.get(origin)">Zurück</MyButton>
        </div>
    </div>
</template>
