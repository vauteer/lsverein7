<script setup>
import { useForm, Head } from "@inertiajs/inertia-vue3";
import MyTextInput from "@/Shared/MyTextInput.vue";
import MyButton from "@/Shared/MyButton.vue"

let form = useForm({
    email: '',
    password: '',
});

let submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div>
        <Head title="Login Page" />

        <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-100">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!--                <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow" />-->
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Anmelden</h2>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <form @submit.prevent="submit" class="space-y-6" action="#" method="POST">
                        <MyTextInput class="sm:col-span-6" v-model="form.email"
                                   :error="form.errors.email" id="email"
                                   label="Email"/>
                        <MyTextInput v-model="form.password"
                                   :error="form.errors.password" id="password"
                                   label="Passwort" type="password"/>

                        <div class="flex items-center justify-between">
                            <div class="text-sm">
                                <a href="/forgot-password" class="font-medium text-indigo-600 hover:text-indigo-500"> Passwort vergessen?</a>
                            </div>
                        </div>

                        <MyButton type="submit" :disabled="form.processing" class="w-full">
                            Anmelden
                        </MyButton>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
