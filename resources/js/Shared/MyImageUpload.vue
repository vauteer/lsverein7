<script>
export default {
    inheritAttrs: false
}
</script>

<script setup>
import { onMounted, ref} from 'vue';
import { Dropzone } from 'dropzone';

const props = defineProps({
    label: String,
    imageUrl: String,
    uploadUrl: {
        type: String,
        default: '/api/uploadimage/'
    },
    location: String,
    resizeWidth: String,
    resizeHeight: String,
});

const emit = defineEmits(['change']);

const dropzone = ref(null);
const currentImageUrl = ref(null);

function removeImage() {
    currentImageUrl.value = null;
    emit('change', null);
}

onMounted(() => {
    currentImageUrl.value = props.imageUrl;

    const options = {
        clickable: ['#upload-text'],
        paramName: 'image',
        url: props.uploadUrl + props.location,
        acceptedFiles: 'image/*',
        disablePreviews: true,
        resizeWidth: props.resizeWidth,
        resizeHeight: props.resizeHeight,
        success: (e, res) => {
            currentImageUrl.value = res.links.self;
            emit('change', res.data.filename)
        },
    };

    dropzone.value = new Dropzone("div#my-dropzone", options);
})
</script>

<template>
    <div v-bind="$attrs">
        <label v-if="label" class="text-sm font-medium text-gray-700"> {{
                label
            }}</label>
        <div class="max-w-lg flex justify-center px-4 pt-3 pb-4 border-2 border-gray-300 border-dashed rounded-md"
             id="frame"
        >
            <div class="space-y-1 text-center dropzone" id="my-dropzone">
                <div class="font-medium text-indigo-900 cursor-pointer dz-message" id="upload-text">
                    Upload a image
                </div>
                <div v-if="currentImageUrl" class="h-20 w-20 overflow-hidden">
                    <img :src="currentImageUrl" alt="image" class="w-full" id="current-image">
                </div>
                <svg v-else class="mx-auto h-20 w-20 text-gray-900" stroke="currentColor" fill="none" viewBox="0 0 48 48"
                     aria-hidden="true" id="default-image">
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <label v-if="currentImageUrl" @click="removeImage" class="cursor-pointer">
                    Löschen
                </label>
            </div>
        </div>
    </div>
</template>
