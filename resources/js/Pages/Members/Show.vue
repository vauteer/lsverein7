<script setup>
import {Head, Link} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {XMarkIcon} from '@heroicons/vue/24/outline';
import MyLayout from '@/Shared/MyLayout.vue';
import MyButton from "@/Shared/MyButton.vue";

let props = defineProps({
    origin: String,
    advanced: Boolean,
    member: Object,
    birthday: String,
    death_day: String,
    age: Number,
    entry: String,
    membershipYears: Number,
    memberClubs: Object,
    memberSections: Object,
    memberRoles: Object,
    memberEvents: Object,
    memberSubscriptions: Object,
});

</script>

<template>
    <MyLayout>
        <button
            tabindex="-1"
            class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"
        ></button>
        <div
            class="relative z-30 w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ member.first_name }}
                            {{ member.surname }}</h3>
                        <Link type="button" :href="origin"
                              class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span class="sr-only">Close</span>
                            <XMarkIcon class="h-5 w-5" aria-hidden="true"/>
                        </Link>
                    </div>
                    <div class="flex justify-between">
                        <p class="mt-1 text-sm text-gray-500">{{ birthday }} <span v-if="death_day"> - {{
                                death_day
                            }}</span></p>
                        <p class="mt-1 text-sm text-gray-500">{{ age }} Jahre</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="mt-1 text-sm text-gray-500">Eingetreten am {{ entry }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ membershipYears }} Jahre</p>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.street }}</dd>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.zipcode }} {{ member.city }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Kontakt</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.phone }}</dd>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.email }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Mitgliedschaft(en)</dt>
                            <dd class="mt-1 text-sm text-gray-900" v-for="membership in memberClubs.data">
                                {{ membership.range }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Sparten</dt>
                            <dd class="mt-1 text-sm text-gray-900" v-for="section in memberSections.data">
                                {{ section.name }} {{ section.range }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Funktionen</dt>
                            <dd class="mt-1 text-sm text-gray-900" v-for="role in memberRoles.data">
                                {{ role.name }} {{ role.range }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Ereignisse</dt>
                            <dd class="mt-1 text-sm text-gray-900" v-for="event in memberEvents.data">
                                {{ event.date }} {{ event.name }}
                            </dd>
                        </div>
                        <div v-if="advanced" class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Bankverbindung</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.account_owner }}</dd>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.bank }}</dd>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.iban }}</dd>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.bic }}</dd>
                        </div>
                        <div v-if="advanced" class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Beiträge</dt>
                            <dd class="mt-1 text-sm text-gray-900" v-for="subscription in memberSubscriptions.data">
                                {{ subscription.name }}
                            </dd>
                        </div>
                        <div v-if="member.memo" class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Memo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ member.memo }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="flex justify-center border-t border-gray-200 px-4 py-4 sm:px-6">
                    <MyButton theme="abort" @click="Inertia.get(origin)">Zurück</MyButton>
                </div>
            </div>
        </div>
    </MyLayout>
</template>
