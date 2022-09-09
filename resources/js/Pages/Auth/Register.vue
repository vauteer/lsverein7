<script setup>
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
import MyTextInput from '@/Shared/MyTextInput.vue';
import MyButton from '@/Shared/MyButton.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register"/>

    <form @submit.prevent="submit">
        <MyTextInput v-model="form.name"
                   :error="form.errors.name" id="name"
                   label="Name" type="text" required/>

        <MyTextInput v-model="form.email"
                   :error="form.errors.email" id="email"
                   label="Email" type="email" required/>

        <MyTextInput v-model="form.password"
                   :error="form.errors.password" id="password"
                   label="Passwort" type="password" required/>

        <MyTextInput v-model="form.password_confirmation"
                   :error="form.errors.password_confirmation" id="password_confirmation"
                   label="Passwort bestätigen" type="password" required/>

        <div class="flex items-center justify-end mt-4">
            <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                Already registered?
            </Link>

            <MyButton type="submit" :disabled="form.processing" class="w-full">
                Register
            </MyButton>
        </div>
    </form>
</template>
