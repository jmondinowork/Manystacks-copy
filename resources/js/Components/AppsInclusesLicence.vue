<script setup>
import { computed } from 'vue';
import { slugify } from '@/functions';
import { Link } from '@inertiajs/vue3';

const data = defineProps({
    currentProduct: {
        type: Object,
    },
});

const apps = computed(() => {
    return data.currentProduct.appsincluses.split(',').map(app => app.trim());
});

</script>

<template>
    <div class="componentcontainer h-full">
        <div class="w_container vertical gap20px">
            <div class="text14px semibold" v-if="data.currentProduct.appstype === 'complete'">
                Services cloud sécurisés et applications de bureau, web et mobiles :
            </div>
            <div class="text14px semibold" v-else-if="data.currentProduct.appstype === 'web'">
                Services cloud sécurisés et applications web et mobiles uniquement :
            </div>
            <div class="text14px semibold" v-else-if="data.currentProduct.appstype === 'inclu'">
                Cette licence est aussi comprise dans les licences suivantes :
            </div>
            <div class="text14px semibold" v-else-if="data.currentProduct.appstype === 'addon'">
                Cette licence est un module complémentaire :
            </div>

            <div class="grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-2 gap-4 items-baseline justify-center"
                v-if="data.currentProduct.appstype === 'complete' || data.currentProduct.appstype === 'web'">
                <div v-for="app in apps" :key="app" class="flex flex-col gap-2 items-center">
                    <img :src="`/images/${app}.png`" loading="lazy" alt="" class="w-8">
                    <div class="text14px text-center">{{ app }}</div>
                </div>
            </div>

            <div class="flex flex-col gap-4 items-start justify-start"
                v-else-if="data.currentProduct.appstype === 'inclu'">
                <Link :href="`/catalogue/licences/licences/${slugify(app)}`" v-for="app in apps" :key="app"
                    class="flex gap-1 cursor-pointer justify-start items-center">
                <div class="text14px purple underline inline-flex items-center">
                    <span class="inline-block">
                        {{ app }}
                        <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                            loading="lazy" width="16" height="16" alt="" class="inline-block w-4 h-4 align-middle">
                    </span>
                </div>
                </Link>
            </div>

            <div v-else>
                {{ data.currentProduct.appsincluses }}
            </div>

        </div>
    </div>
</template>
