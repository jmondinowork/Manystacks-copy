<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const specialSegments = ['mes-stacks', 'mon-catalogue', 'panier'];
const breadcrumbs = computed(() => {
    let pathSegments = window.location.pathname.split('/').filter(Boolean);

    let crumbs = pathSegments.map((segment, index, array) => {
        let cleanedSegment = segment.replace(/\([^()]*\)(?!.*\([^()]*\))/, '');

        return {
            label: cleanedSegment.charAt(0).toUpperCase() + cleanedSegment.slice(1).replace(/-/g, ' '),
            url: '/' + array.slice(0, index + 1).join('/')
        };
    });

    if (specialSegments.includes(pathSegments[0]))
        crumbs.unshift({ label: 'Catalogue', url: '/catalogue' });

    return crumbs;
});

</script>

<template>
    <nav v-if="breadcrumbs.length" class="flex items-center">
        <template v-for="(breadcrumb, index) in breadcrumbs" :key="index">
            <Link v-if="index !== breadcrumbs.length - 1" :href="breadcrumb.url" class="hover:text-blue-600">{{
                breadcrumb.label }}</Link>
            <span v-else class="text14px medium purple">{{ breadcrumb.label }}</span>
            <img v-if="index < breadcrumbs.length - 1" src="/images/breadscrumb-arrow.png" class="arrow" alt="">
        </template>
    </nav>
</template>

<style scoped>
.arrow {
    width: 14px;
    height: 14px;
    margin: 0 5px;
}
</style>
