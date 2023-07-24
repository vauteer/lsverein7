<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MyLayout from '@/Shared/MyLayout.vue';
import MyTextInput from '@/Shared/MyTextInput.vue';
import MyButton from '@/Shared/MyButton.vue'

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    })
};
</script>

<template>
    <Head title="Passwort bestätigen" />

    <MyLayout>
        <div class="min-h-full flex flex-col justify-center sm:px-6 lg:px-8 bg-gray-100">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!--                <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow" />-->
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Passwort bestätigen</h2>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <form @submit.prevent="submit" class="space-y-6" action="#" method="POST">
                        <MyTextInput class="sm:col-span-6" v-model="form.password"
                                   :error="form.errors.password" id="password" type="password"
                                   required autocomplete="current-password" autofocus
                                   label="Passwort"/>

                        <MyButton type="submit" :disabled="form.processing" :class="{ 'opacity-25': form.processing }"
                                      class="w-full">
                            Bestätigen
                        </MyButton>
                    </form>
                </div>
            </div>
        </div>
    </MyLayout>
</template>
