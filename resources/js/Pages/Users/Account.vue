<script setup>
import { onMounted, computed } from "vue";
import { useForm, Head } from "@inertiajs/inertia-vue3";
import EditTitle from "@/Shared/EditTitle.vue";
import ImageUpload from "@/Shared/ImageUpload.vue";
import TextInput from "@/Shared/TextInput.vue";
import AbortButton from "@/Shared/AbortButton.vue";
import SubmitButton from "@/Shared/SubmitButton.vue";
import Layout from "@/Shared/Layout.vue";

let props = defineProps({
    user: Object,
});

let form = useForm({
    name: '',
    email: '',
    profile_image: '',
    current_password: '',
    password: '',
    password_confirmation: '',
});

onMounted(() => {
    if (props.user !== undefined) {
        form.name = props.user.name;
        form.email = props.user.email;
    }
});

let submit = () => {
    form.put('/users/account');
};

const getProfileImageUrl = computed(() => {
    return props.user?.profile_image ? '/storage/profile/' + props.user.profile_image : null;
})

function onProfileImageChanged(filename) {
    form.profile_image = filename;
}

function back() {
    window.history.back();
}

</script>
<template>
    <Head title="Konto" />

    <Layout>
        <div>
            <button tabindex="-1" class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"></button>
            <div class="relative z-30 w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
                <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
                    <EditTitle class="ml-3 mb-4">Konto</EditTitle>

                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                        <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                            <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                                <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                                    <ImageUpload
                                        label="Profile Image"
                                        class="sm:col-span-2"
                                        id="profile-image"
                                        :image-url="getProfileImageUrl"
                                        location="profile"
                                        resize-height="150"
                                        v-on:change="onProfileImageChanged"
                                    />
                                    <div class="sm:col-span-4 grid grid-cols-1 gap-y-4">
                                        <TextInput v-model="form.name" :error="form.errors.name"
                                                   id="name" label="Name"/>
                                        <TextInput v-model="form.email" :error="form.errors.email" id="email"
                                                   label="Email"/>
                                        <div class="w-full border-t"></div>
                                        <TextInput v-model="form.current_password"
                                                   :error="form.errors.current_password" id="current-password"
                                                   label="Passwort" type="password"/>
                                        <TextInput v-model="form.password"
                                                   :error="form.errors.password"
                                                   id="password" label="Neues Passwort" type="password"/>
                                        <TextInput v-model="form.password_confirmation"
                                                   :error="form.errors.password_confirmation"
                                                   id="password-confirmation" label="Passwort bestätigen" type="password"/>
                                    </div>
                                </div>
                                <div class="py-5">
                                    <div class="flex justify-end">
                                        <AbortButton @click="back" />
                                        <SubmitButton class="mx-2" :disabled="form.processing" />
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </Layout>
</template>
