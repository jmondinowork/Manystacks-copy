<script setup>
import CatalogueLayout from '@/Layouts/CatalogueLayout.vue';
import ProductsPreview from '@/Components/ProductsPreview.vue';
import StacksPublicPreview from '@/Components/StacksPublicPreview.vue';

import { Head, usePage } from '@inertiajs/vue3';

const { props } = usePage();
</script>

<template>
    <Head>
        <title>Catalogue</title>
        <meta name="description" content="Retrouvez tous nos produits">
    </Head>

    <CatalogueLayout>
        <StacksPublicPreview v-if="props.stacksPublic.length"></StacksPublicPreview>

        <ProductsPreview :filteredProducts="props.products" :title="'Nos équipements populaires'"></ProductsPreview>

        <!-- <HighlightProduct></HighlightProduct>  -->
    </CatalogueLayout>
</template>
