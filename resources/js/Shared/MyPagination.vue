<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

let props = defineProps({
    meta: Object,
});

let getUrl = function(page) {
  if (page < 1 || page > props.meta.last_page) {
      return null;
  } else {
      return props.meta.links[1].url.replace(/page=\d+/, 'page=' + page);
  }
};

let getLinks = computed(() => {
    let links = Array();

    if (props.meta.current_page > 2) {
        links.push({
            active: false,
            label: "1",
            url: getUrl(1),
        });
    }

    if (props.meta.current_page > 3) {
        links.push({
            active: false,
            label: "...",
            url: null,
        });
    }

    for (let i = props.meta.current_page - 1; i < props.meta.current_page + 2; i++) {
        let url = getUrl(i);
        if (url) {
            links.push({
                active: i === props.meta.current_page,
                label: String(i),
                url: url,
            });
        }
    }

    if (props.meta.current_page < (props.meta.last_page - 2)) {
        links.push({
            active: false,
            label: "...",
            url: null,
        });
    }

    if (props.meta.last_page > props.meta.current_page + 1) {
        links.push({
            active: false,
            label: String(props.meta.last_page),
            url: getUrl(props.meta.last_page),
        });
    }

    return links;
});

</script>

<template>
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <Link :href="getUrl(meta.current_page - 1)" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                </Link>

                <div v-for="link in getLinks">
                    <p v-if="link.active"
                       aria-current="page"
                       class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-3.5 py-2 border text-sm font-medium"> {{ link.label }}
                    </p>
                    <p v-else-if="!link.url"
                          class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-3.5 py-2 border text-sm font-medium">
                        {{ link.label }}
                    </p>
                    <Link v-else :href="link.url"
                          class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-3.5 py-2 border text-sm font-medium">
                        {{ link.label }}
                    </Link>
                </div>

                <Link :href="getUrl(meta.current_page + 1)" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                </Link>
            </nav>
        </div>
    </div>
</template>
