<script setup>
import { computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import { usePage } from "@inertiajs/vue3";

const user = computed(() => usePage().props.auth.user);
const club = computed(() => usePage().props.auth.club);
const flashSuccess = computed(() => usePage().props.flash.success);
const flashError = computed(() => usePage().props.flash.error);

const getNavigation = computed(() => {
    return [
        { name: 'Mitglieder', route: 'members', visible: true },
        { name: 'Abteilungen', route: 'sections', visible: true },
        { name: 'Ereignisse', route: 'events', visible: true },
        { name: 'Funktionen', route: 'roles', visible: true },
        { name: 'Beiträge', route: 'subscriptions', visible: user.value.clubAdmin },
        { name: 'Lastschriften', route: 'debits', visible: user.value.clubAdmin },
        { name: 'Inventar', route: 'items', visible: club.value.useItems },
    ];
})

let logout = () => {
    router.post("/logout");
};
</script>

<template>
    <div class="min-h-full">
        <Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div v-if="club" class="flex items-center">
                            <img v-if="club.showLogo" class="h-10 mr-3" :src="club.logoUrl" alt="logo" />
                            <div v-if="club.showName" class="text-white text-xl">{{ club.name }}</div>
                        </div>
                        <div v-else>
                            <div class="text-white text-xl ml-12">LS-Verein<span class="text-sm"> by</span></div>
                            <img class="h-10 -mt-3" src="/logo.png" alt="Workflow" />
                        </div>
                        <div v-if="club" class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <div v-for="item in getNavigation" :key="item.name" >
                                    <Link v-if="item.visible" :href="route(item.route)"
                                          :class="[route().current(item.route) ? 'bg-gray-900 text-orange-400' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'px-3 py-2 rounded-md text-sm font-medium']"
                                    >
                                        {{ item.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <!-- Profile dropdown -->
                            <Menu as="div" class="ml-3 relative">
                                <div>
                                    <MenuButton class="max-w-xs bg-gray-800 rounded-full flex items-center text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-10 rounded-full" :src="user.profileUrl" alt="" />
                                    </MenuButton>
                                </div>
                                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems class="z-50 origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <MenuItem disabled>
                                            <span class="block px-4 py-2 text-sm text-gray-700 opacity-75">{{ user.name }}</span>
                                        </MenuItem>
                                        <MenuItem v-if="user.clubAdmin" v-slot="{ active, close }">
                                            <Link :href="route('users')" preserve-state @click="close"
                                                  :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">
                                                Benutzer
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-if="user.clubAdmin" v-slot="{ active, close }">
                                            <Link :href="route('clubs')" preserve-state @click="close"
                                                  :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">
                                                Vereine
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-if="user.admin" v-slot="{ active, close }">
                                            <Link :href="route('backups')" preserve-state @click="close"
                                                  :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">
                                                Backups
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-if="user.admin" v-slot="{ active, close }">
                                            <Link :href="route('export.club')" preserve-state @click="close"
                                                  :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">
                                                Export
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active, close }">
                                            <Link :href="route('account.edit')" preserve-state @click="close"
                                                  :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">
                                                Mein Konto
                                            </Link>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active }">
                                            <div @click="logout"
                                                 :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700 cursor-pointer']">
                                                Abmelden
                                            </div>
                                        </MenuItem>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <!-- Mobile menu button -->
                        <DisclosureButton class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                        </DisclosureButton>
                    </div>
                </div>
            </div>

            <DisclosurePanel class="md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <Link v-for="item in getNavigation" :key="item.name" as="a" :href="route(item.route)" >
                        <DisclosureButton v-if="item.visible" :class="[route().current(item.route) ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'block px-3 py-2 rounded-md text-base font-medium']">{{ item.name }}</DisclosureButton>
                    </Link>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-700">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full" :src="user.profileUrl" alt="" />
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-white">{{ user.name }}</div>
                            <div class="text-sm font-medium text-gray-400">{{ user.email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        <DisclosureButton v-if="user.clubAdmin" as="a" :href="route('users')"
                                          class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">
                            Benutzer
                        </DisclosureButton>
                        <DisclosureButton v-if="user.admin" as="a" :href="route('clubs')"
                                          class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">
                            Vereine
                        </DisclosureButton>
                        <DisclosureButton v-if="user.admin" as="a" :href="route('backups')"
                                          class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">
                            Backups
                        </DisclosureButton>
                        <DisclosureButton v-if="user.admin" as="a" :href="route('export')"
                                          class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">
                            Export
                        </DisclosureButton>
                        <DisclosureButton as="a" :href="route('account.edit')"
                                          class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">
                            Mein Konto
                        </DisclosureButton>
                        <DisclosureButton @click="logout"
                                          class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">
                            Abmelden
                        </DisclosureButton>
                    </div>
                </div>
            </DisclosurePanel>
        </Disclosure>

        <main>
            <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
                <div v-if="flashSuccess" class="rounded-md bg-green-50 p-4">
                    <div class="flex justify-center">
                        <div class="flex-shrink-0">
                            <CheckCircleIcon class="h-5 w-5 text-green-400" aria-hidden="true" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">{{ flashSuccess }}</h3>
                        </div>
                    </div>
                </div>
                <div v-if="flashError" class="rounded-md bg-red-50 p-4">
                    <div class="flex justify-center">
                        <div class="flex-shrink-0">
                            <XCircleIcon class="h-5 w-5 text-red-400" aria-hidden="true" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ flashError }}</h3>
                        </div>
                    </div>
                </div>
                <slot />
            </div>
        </main>
    </div>
</template>
