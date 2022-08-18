<template>
    <div>
        <Head title="Passwort vergessen" />

        <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!--                <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow" />-->
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Passwort vergessen</h2>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="mb-4 text-center text-sm font-medium text-gray-900">
                        Passwort vergessen ? Kein Problem. Teilen Sie uns einfach ihre Email-Adresse mit
                        und wir werden Ihnen einen Reset-Link für das Passwort per Email zusenden
                    </div>
                    <form @submit.prevent="submit" class="space-y-6" action="#" method="POST">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700"> Email</label>
                            <div class="mt-1">
                                <input v-model="form.email" id="email" name="email" type="email" autocomplete="email" required="" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                                <div v-if="form.errors.email" v-text="form.errors.email" class="text-red-500 text-xs mt-1"></div>
                            </div>
                        </div>

                        <div>
                            <button type="submit" :disabled="form.processing"
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Reset Link senden
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    layout: null,
}
</script>

<script setup>
import { useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

let submit = () => {
    form.post('/forgot-password');
};
</script>
