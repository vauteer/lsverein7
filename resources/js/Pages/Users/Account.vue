<script setup>
import { onMounted, computed } from "vue";
import { router, useForm, Head } from "@inertiajs/vue3";
import MyImageUpload from "@/Shared/MyImageUpload.vue";
import MyTextInput from "@/Shared/MyTextInput.vue";
import MyButton from "@/Shared/MyButton.vue";

let props = defineProps({
    user: Object,
    origin: String,
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
    form.put(route('account.update'));
};

const profileUrl = computed(() => {
    return props.user?.profile_image ? '/storage/profile/' + props.user.profile_image : null;
})

function onProfileImageChanged(filename) {
    form.profile_image = filename;
}

</script>
<template>
    <Head title="Konto"/>
    <button tabindex="-1"
            class="hidden md:block fixed z-20 inset-0 h-full w-full bg-black opacity-50 cursor-default"></button>
    <div
        class="relative z-30 w-full max-w-xl mx-auto bg-gray-100 text-gray-900 text-sm sm:rounded sm:border sm:shadow sm:overflow-hidden mt-2">
        <div class="sm:px-2 lg:px-4 sm:py-2 lg:py-4">
            <div class="font-medium text-lg text-gray-900 ml-3 mb-4">Konto</div>

            <div
                class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg sm:px-2 lg:px-4 bg-white">
                <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200">
                    <div class="space-y-8 divide-y divide-gray-200 my-3 mx-2">
                        <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                            <MyImageUpload
                                label="Profilbild"
                                class="sm:col-span-2"
                                id="profile-image"
                                :image-url="profileUrl"
                                location="profile"
                                resize-height="150"
                                v-on:change="onProfileImageChanged"
                            />
                            <div class="sm:col-span-4 grid grid-cols-1 gap-y-4">
                                <MyTextInput v-model="form.name" :error="form.errors.name"
                                             id="name" label="Name"/>
                                <MyTextInput v-model="form.email" :error="form.errors.email" id="email"
                                             label="Email"/>
                                <div class="w-full border-t"></div>
                                <MyTextInput v-model="form.current_password"
                                             :error="form.errors.current_password" id="current-password"
                                             label="Passwort" type="password"/>
                                <MyTextInput v-model="form.password"
                                             :error="form.errors.password"
                                             id="password" label="Neues Passwort" type="password"/>
                                <MyTextInput v-model="form.password_confirmation"
                                             :error="form.errors.password_confirmation"
                                             id="password-confirmation" label="Passwort bestätigen"
                                             type="password"/>
                            </div>
                        </div>
                        <div class="py-5">
                            <div class="flex justify-end">
                                <MyButton theme="abort" @click="router.get(origin)">Abbrechen</MyButton>
                                <MyButton type="submit" class="mx-2" :disabled="form.processing">Speichern
                                </MyButton>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
