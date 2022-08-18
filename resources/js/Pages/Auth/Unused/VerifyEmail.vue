<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import Layout from '@/Shared/Layout.vue';
import SubmitButton from '@/Shared/SubmitButton.vue';

const props = defineProps({
    status: String,
});

const form = useForm();

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Layout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-gray-600">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent" >
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <SubmitButton :disabled="form.processing" class="w-full">
                    Resend Verification Email
                </SubmitButton>

                <Link :href="route('logout')" method="post" as="button" class="underline text-sm text-gray-600 hover:text-gray-900">Log Out</Link>
            </div>
        </form>
    </Layout>
</template>
