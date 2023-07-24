<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import MyTextInput from "@/Shared/MyTextInput.vue";
import MyButton from "@/Shared/MyButton.vue";

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

<template>
    <div>
        <Head title="Passwort vergessen" />

        <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-100">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!--                <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow" />-->
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Passwort vergessen</h2>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="mb-4 text-center text-sm font-medium text-gray-900">
                        Passwort vergessen? Kein Problem. Teilen Sie uns einfach ihre Email-Adresse mit
                        und wir werden Ihnen einen Reset-Link für das Passwort per Email zusenden
                    </div>
                    <form @submit.prevent="submit" class="space-y-6" action="#" method="POST">
                        <div>
                            <MyTextInput class="sm:col-span-6" v-model="form.email"
                                         :error="form.errors.email" id="email"
                                         label="Email"/>
                        </div>
                        <div v-if="status">{{ status }}</div>
                        <MyButton class="w-full" type="submit" :disabled="form.processing">
                            Reset Link anfordern
                        </MyButton>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

